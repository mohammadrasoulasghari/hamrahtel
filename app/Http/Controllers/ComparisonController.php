<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use App\Services\OpenRouterService;
use App\Services\ColumnDetectionService;
use App\Services\RowJoiningService;
use App\Services\ComparisonAnalyzer;
use App\Services\TimeEstimator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ComparisonController extends Controller
{
    protected $openRouterService;
    protected $columnDetectionService;
    protected $rowJoiningService;
    protected $comparisonAnalyzer;
    protected $timeEstimator;

    public function __construct(
        OpenRouterService $openRouterService,
        ColumnDetectionService $columnDetectionService,
        RowJoiningService $rowJoiningService,
        ComparisonAnalyzer $comparisonAnalyzer,
        TimeEstimator $timeEstimator
    ) {
        $this->openRouterService = $openRouterService;
        $this->columnDetectionService = $columnDetectionService;
        $this->rowJoiningService = $rowJoiningService;
        $this->comparisonAnalyzer = $comparisonAnalyzer;
        $this->timeEstimator = $timeEstimator;
        
        // Set PHP ini settings for large file handling
        @ini_set('upload_max_filesize', '50M');
        @ini_set('post_max_size', '50M');
        @ini_set('max_execution_time', '300');
        @ini_set('max_input_time', '300');
        @ini_set('memory_limit', '512M');
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file1' => 'required|file|mimes:xlsx,xls,csv|max:510200',
                'file2' => 'required|file|mimes:xlsx,xls,csv|max:512000',
                'description' => 'nullable|string|max:5000',
            ]);

            // آپلود فایل‌ها
            $file1Path = $request->file('file1')->store('uploads', 'public');
            $file2Path = $request->file('file2')->store('uploads', 'public');

            // تبدیل فایل‌ها به JSON
            $file1Json = $this->convertToJson(storage_path('app/public/' . $file1Path));
            $file2Json = $this->convertToJson(storage_path('app/public/' . $file2Path));

            // ذخیره در دیتابیس
            $comparison = Comparison::create([
                'file1_path' => $file1Path,
                'file2_path' => $file2Path,
                'file1_original_name' => $request->file('file1')->getClientOriginalName(),
                'file2_original_name' => $request->file('file2')->getClientOriginalName(),
                'file1_json' => json_encode($file1Json),
                'file2_json' => json_encode($file2Json),
                'description' => $request->input('description'),
                'status' => 'processing',
            ]);

            // ارسال به AI برای مقایسه
            $aiResult = $this->openRouterService->compareFiles(
                $file1Json,
                $file2Json,
                $request->input('description', 'مقایسه فایل‌های اکسل')
            );

            // به‌روزرسانی نتیجه
            $comparison->update([
                'ai_result' => $aiResult,
                'status' => 'completed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'مقایسه با موفقیت انجام شد',
                'comparison_id' => $comparison->id,
                'redirect_url' => route('comparison.result', $comparison->id),
            ]);

        } catch (\Exception $e) {
            Log::error('خطا در آپلود و مقایسه فایل‌ها: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در پردازش فایل‌ها: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function result($id)
    {
        $comparison = Comparison::findOrFail($id);
        
        return view('result', compact('comparison'));
    }

    /**
     * Preview columns from uploaded files (Phase 1: Schema Detection).
     */
    public function previewColumns(Request $request)
    {
        try {
            $request->validate([
                'file1' => 'required|file|mimes:xlsx,xls,csv|max:51200',
                'file2' => 'required|file|mimes:xlsx,xls,csv|max:51200',
            ]);

            // Upload files temporarily
            $file1Path = $request->file('file1')->store('temp', 'public');
            $file2Path = $request->file('file2')->store('temp', 'public');

            $file1FullPath = storage_path('app/public/' . $file1Path);
            $file2FullPath = storage_path('app/public/' . $file2Path);

            // Detect schemas
            $schema1 = $this->columnDetectionService->detectColumns($file1FullPath);
            $schema2 = $this->columnDetectionService->detectColumns($file2FullPath);

            // Estimate processing time
            $timeEstimate = $this->timeEstimator->estimateProcessingTime(
                $schema1['row_count'],
                $schema2['row_count'],
                config('comparison.ai_model'),
                config('comparison.enable_ai_analysis')
            );

            // Create comparison record
            $comparison = Comparison::create([
                'file1_path' => $file1Path,
                'file2_path' => $file2Path,
                'file1_original_name' => $request->file('file1')->getClientOriginalName(),
                'file2_original_name' => $request->file('file2')->getClientOriginalName(),
                'file1_columns' => $schema1['columns'],
                'file2_columns' => $schema2['columns'],
                'status' => 'preview',
            ]);

            return response()->json([
                'success' => true,
                'comparison_id' => $comparison->id,
                'file1' => [
                    'columns' => $schema1['columns'],
                    'row_count' => $schema1['row_count'],
                    'data_types' => $schema1['data_types'],
                    'sample_data' => $schema1['sample_data'],
                ],
                'file2' => [
                    'columns' => $schema2['columns'],
                    'row_count' => $schema2['row_count'],
                    'data_types' => $schema2['data_types'],
                    'sample_data' => $schema2['sample_data'],
                ],
                'time_estimate' => $timeEstimate,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in previewColumns: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در بررسی فایل‌ها: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate column matching and show preview of matched rows.
     * Uses lightweight sampling to avoid memory exhaustion.
     */
    public function validateMatching(Request $request)
    {
        try {
            $request->validate([
                'comparison_id' => 'required|exists:comparisons,id',
                'key_columns' => 'required|array',
                'join_strategy' => 'required|string',
            ]);

            $comparison = Comparison::findOrFail($request->comparison_id);

            // Load only first 100 rows for preview (to avoid memory exhaustion)
            $file1Data = $this->loadSampleRows(storage_path('app/public/' . $comparison->file1_path), 100);
            $file2Data = $this->loadSampleRows(storage_path('app/public/' . $comparison->file2_path), 100);

            // Perform row joining on sample data
            if ($request->join_strategy === 'compare_all') {
                $joinResult = $this->rowJoiningService->compareAll($file1Data, $file2Data);
            } else {
                $joinResult = $this->rowJoiningService->joinByKeyColumns(
                    $file1Data,
                    $file2Data,
                    $request->key_columns,
                    $request->join_strategy
                );
            }

            // Get sample of matched rows
            $sampleMatched = array_slice($joinResult['matched'], 0, 5);

            return response()->json([
                'success' => true,
                'matched_count' => count($joinResult['matched']),
                'unmatched_file1_count' => count($joinResult['unmatched_file1']),
                'unmatched_file2_count' => count($joinResult['unmatched_file2']),
                'sample_matched' => $sampleMatched,
                'note' => 'این آمار بر اساس 100 ردیف اول است. نتیجه نهایی ممکن است متفاوت باشد.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error in validateMatching: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در بررسی تطابق: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start full comparison with selected configuration.
     * Dispatches job to queue for async processing.
     */
    public function startComparison(Request $request)
    {
        try {
            $request->validate([
                'comparison_id' => 'required|exists:comparisons,id',
                'key_columns' => 'nullable|array',
                'join_strategy' => 'required|string',
                'description' => 'nullable|string|max:5000',
            ]);

            $comparison = Comparison::findOrFail($request->comparison_id);
            
            // Update status to processing
            $comparison->update([
                'selected_key_columns' => $request->key_columns,
                'row_join_strategy' => $request->join_strategy,
                'description' => $request->description,
                'status' => 'processing',
            ]);

            // Dispatch job to queue for async processing
            \App\Jobs\ProcessFileComparison::dispatch(
                $comparison->id,
                $request->key_columns ?? [],
                $request->join_strategy,
                $request->description
            );

            return response()->json([
                'success' => true,
                'message' => 'پردازش شروع شد. لطفاً صبر کنید...',
                'comparison_id' => $comparison->id,
                'status' => 'processing',
            ]);

        } catch (\Exception $e) {
            Log::error('Error in startComparison: ' . $e->getMessage());
            
            if (isset($comparison)) {
                $comparison->update(['status' => 'failed']);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'خطا در شروع پردازش: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check processing status of a comparison job.
     */
    public function checkStatus($id)
    {
        try {
            $comparison = Comparison::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'status' => $comparison->status,
                'matched_count' => $comparison->matched_count,
                'unmatched_file1_count' => $comparison->unmatched_file1_count,
                'unmatched_file2_count' => $comparison->unmatched_file2_count,
                'processing_started_at' => $comparison->processing_started_at?->format('Y-m-d H:i:s'),
                'processing_completed_at' => $comparison->processing_completed_at?->format('Y-m-d H:i:s'),
                'redirect_url' => $comparison->status === 'completed' 
                    ? route('comparison.result', $comparison->id) 
                    : null,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در بررسی وضعیت: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Load only sample rows from file to avoid memory exhaustion.
     * Uses PhpSpreadsheet to read limited rows.
     *
     * @param string $filePath
     * @param int $limit
     * @return array
     */
    private function loadSampleRows(string $filePath, int $limit = 100): array
    {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Get headers (first row)
            $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
            
            // Get sample data rows
            $rows = [];
            $maxRow = min($sheet->getHighestRow(), $limit + 1);
            
            for ($row = 2; $row <= $maxRow; $row++) {
                $rowData = $sheet->rangeToArray(
                    'A' . $row . ':' . $sheet->getHighestColumn() . $row
                )[0];
                
                // Map to headers
                $mappedRow = [];
                foreach ($headers as $idx => $header) {
                    $mappedRow[$header] = $rowData[$idx] ?? null;
                }
                $rows[] = $mappedRow;
            }
            
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            return $rows;
        } catch (\Exception $e) {
            Log::error('Error loading sample rows: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Convert Excel file to JSON array (DEPRECATED - use Job for large files).
     * Uses PhpSpreadsheet chunked reading for memory safety.
     * 
     * @deprecated Use ProcessFileComparison job instead for production
     */
    private function convertToJson($filePath)
    {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Get headers
            $headers = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];
            
            // Get all data rows
            $data = [];
            $highestRow = $sheet->getHighestRow();
            $chunkSize = 500;
            
            for ($startRow = 2; $startRow <= $highestRow; $startRow += $chunkSize) {
                $endRow = min($startRow + $chunkSize - 1, $highestRow);
                
                $chunkData = $sheet->rangeToArray(
                    'A' . $startRow . ':' . $sheet->getHighestColumn() . $endRow
                );
                
                foreach ($chunkData as $row) {
                    $rowData = [];
                    foreach ($headers as $index => $header) {
                        $rowData[$header] = $row[$index] ?? null;
                    }
                    $data[] = $rowData;
                }
            }
            
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            return $data;
            
        } catch (\Exception $e) {
            Log::error('Error converting file to JSON: ' . $e->getMessage());
            throw $e;
        }
    }
}
