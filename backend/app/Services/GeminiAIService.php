<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        // ✅ استخدم pro للردود الأطول والأكثر استقراراً
        $this->model = config('services.gemini.model', 'gemini-1.5-pro');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
    }

    public function generateDescription(string $base64Image, string $mimeType, string $title, float $price): string
    {
        $base64Image = $this->cleanBase64($base64Image);

        $prompt = $this->buildPrompt($title, $price);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Image,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.8,
                
                'topP' => 0.95,
                'topK' => 40,
            ],
        ];

        $endpoint = "{$this->baseUrl}/{$this->model}:generateContent";

        try {
            $response = Http::asJson()
                ->withHeaders([
                    'X-goog-api-key' => $this->apiKey,
                ])
                ->timeout(30)
                ->post($endpoint, $payload);

            if ($response->failed()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return $this->fallbackDescription($title, $price);
            }

            $data = $response->json();
            
            // ✅ تحقق من سبب الانتهاء
            $finishReason = $data['candidates'][0]['finishReason'] ?? null;
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if ($finishReason === 'MAX_TOKENS') {
                Log::warning('Gemini response truncated', [
                    'title' => $title,
                    'text_preview' => substr($text, 0, 100),
                ]);
            }

            if (!$text) {
                return $this->fallbackDescription($title, $price);
            }

            return trim($text);

        } catch (\Exception $e) {
            Log::error('Gemini API exception', ['message' => $e->getMessage()]);
            return $this->fallbackDescription($title, $price);
        }
    }

    private function buildPrompt(string $title, float $price): string
    {
        return <<<PROMPT
أنت خبير تسويق محترف. قم بتحليل الصورة المرفقة واكتب وصفاً تسويقياً كاملاً ومفصلاً باللغة العربية الفصحى.

معلومات المنتج:
- العنوان: {$title}
- السعر: {$price} دينار

متطلبات الوصف:
1. ابدأ بجملة افتتاحية قوية تجذب الانتباه
2. اذكر 3-4 مميزات رئيسية يمكن استنتاجها من الصورة
3. اشرح الفائدة للعميل
4. أنهِ الوصف بعبارة تحفيزية للشراء
5. اجعل الوصف بين 40-60 كلمة
6. اكتب جملاً كاملة ولا تقاطع الكلمات
7. لا تستخدم علامات تنصيص أو رموز خاصة
8. اكتب النص بشكل متصل وسلس
PROMPT;
    }

    private function fallbackDescription(string $title, float $price): string
    {
        return "اكتشف عرضاً مميزاً على {$title} بسعر {$price} دينار فقط. منتج عالي الجودة بتصميم أنيق يلبي احتياجاتك اليومية. فرصة لا تُفوت للحصول على أفضل قيمة مقابل السعر. اطلبه الآن واستمتع بتجربة تسوق ممتعة!";
    }

    private function cleanBase64(string $base64): string
    {
        if (str_contains($base64, ',')) {
            return substr($base64, strpos($base64, ',') + 1);
        }
        return $base64;
    }
}