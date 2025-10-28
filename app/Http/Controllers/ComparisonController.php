<?php

namespace App\Http\Controllers;

use App\Models\Comparison;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ComparisonController extends Controller
{
    protected $openRouterService;

    public function __construct(OpenRouterService $openRouterService)
    {
        $this->openRouterService = $openRouterService;
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'file1' => 'required|file|mimes:xlsx,xls,csv|max:10240',
                'file2' => 'required|file|mimes:xlsx,xls,csv|max:10240',
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

    private function convertToJson($filePath)
    {
        try {
            $collection = Excel::toCollection(null, $filePath);
            
            // تبدیل Collection به آرایه ساده
            $data = $collection->first()->toArray();
            
            // استفاده از سطر اول به عنوان هدر
            $headers = array_shift($data);
            
            // ساخت آرایه‌ای با کلیدهای نامگذاری شده
            $result = [];
            foreach ($data as $row) {
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $rowData[$header] = $row[$index] ?? null;
                }
                $result[] = $rowData;
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('خطا در تبدیل فایل به JSON: ' . $e->getMessage());
            throw $e;
        }
    }
}
