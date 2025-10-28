<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('openrouter.api_key');
        $this->model = config('openrouter.model');
        $this->baseUrl = config('openrouter.base_url');
    }

    public function compareFiles($file1Data, $file2Data, $description)
    {
        try {
            // ساخت پرامپت برای AI
            $prompt = $this->buildComparisonPrompt($file1Data, $file2Data, $description);

            // ارسال درخواست به OpenRouter
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(120)->post($this->baseUrl . '/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'شما یک متخصص تحلیل داده هستید که وظیفه دارید دو فایل اکسل را با هم مقایسه کنید و تفاوت‌ها را به صورت دقیق و کامل گزارش دهید. پاسخ خود را به فارسی و با فرمت HTML زیبا و قابل خواندن ارائه دهید.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 4000,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['choices'][0]['message']['content'] ?? 'خطا در دریافت پاسخ از AI';
            }

            Log::error('OpenRouter API Error: ' . $response->body());
            return 'خطا در ارتباط با سرویس هوش مصنوعی: ' . $response->status();

        } catch (\Exception $e) {
            Log::error('OpenRouter Service Error: ' . $e->getMessage());
            return 'خطا در پردازش درخواست: ' . $e->getMessage();
        }
    }

    private function buildComparisonPrompt($file1Data, $file2Data, $description)
    {
        $file1Summary = $this->summarizeData($file1Data);
        $file2Summary = $this->summarizeData($file2Data);

        $prompt = "لطفاً دو فایل اکسل زیر را با هم مقایسه کنید:\n\n";
        $prompt .= "## توضیحات کاربر:\n{$description}\n\n";
        $prompt .= "## فایل اول:\n";
        $prompt .= "تعداد ردیف‌ها: " . count($file1Data) . "\n";
        $prompt .= "ستون‌ها: " . implode(', ', array_keys($file1Data[0] ?? [])) . "\n";
        $prompt .= "نمونه داده‌ها (5 ردیف اول):\n";
        $prompt .= json_encode(array_slice($file1Data, 0, 5), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
        
        $prompt .= "## فایل دوم:\n";
        $prompt .= "تعداد ردیف‌ها: " . count($file2Data) . "\n";
        $prompt .= "ستون‌ها: " . implode(', ', array_keys($file2Data[0] ?? [])) . "\n";
        $prompt .= "نمونه داده‌ها (5 ردیف اول):\n";
        $prompt .= json_encode(array_slice($file2Data, 0, 5), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
        
        $prompt .= "## درخواست:\n";
        $prompt .= "لطفاً موارد زیر را بررسی و گزارش کنید:\n";
        $prompt .= "1. تفاوت در ساختار (ستون‌های مختلف، نوع داده‌ها)\n";
        $prompt .= "2. تفاوت در تعداد ردیف‌ها\n";
        $prompt .= "3. تفاوت‌های محتوایی مهم\n";
        $prompt .= "4. ستون‌هایی که در یک فایل هست ولی در دیگری نیست\n";
        $prompt .= "5. هر مغایرت مهم دیگری که پیدا می‌کنید\n\n";
        $prompt .= "پاسخ را با فرمت HTML زیبا، با استفاده از تگ‌های <div>, <h3>, <ul>, <table> و کلاس‌های Tailwind CSS بنویسید.\n";
        $prompt .= "از رنگ‌های مناسب برای نمایش تفاوت‌ها استفاده کنید (قرمز برای کاهش، سبز برای افزایش و غیره).\n";

        return $prompt;
    }

    private function summarizeData($data)
    {
        if (empty($data)) {
            return 'داده‌ای وجود ندارد';
        }

        $summary = [
            'total_rows' => count($data),
            'columns' => array_keys($data[0] ?? []),
            'sample' => array_slice($data, 0, 3),
        ];

        return $summary;
    }
}
