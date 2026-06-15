<?php

namespace App\Helper;

use App\Models\AIPricing;
use Illuminate\Support\Facades\Log;
use Aws\S3\S3Client;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Helpers
{
    public static function preSignedS3Url(?string $key)
    {
        if (!$key) {
            return '';
        }

        if (Str::startsWith($key, 'http') || Str::contains($key, 'http:') || Str::contains($key, 'https:')) {
            return $key;
        }

        try {
            $s3Client = new S3Client([
                'region' => config('filesystems.disks.s3.region'),
                'version' => 'latest',
            ]);

            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => $key,
            ]);
            $presignedUrlExpiration = '+7 days';

            $request = $s3Client->createPresignedRequest($cmd, $presignedUrlExpiration);

            return (string) $request->getUri();
        } catch (\Exception $exception) {
            // report($exception);
        }

        return $key;
    }

    public static function updatePresignS3($s3Url) {
        if (str_contains($s3Url, "https://adbox-staging.s3.ap-northeast-1.amazonaws.com")) {
            $s3Url = str_replace("https://adbox-staging.s3.ap-northeast-1.amazonaws.com/","",$s3Url);
            $s3Url = explode("?", $s3Url);
            if(count($s3Url) == 2) {
                $s3Url =  self::preSignedS3Url($s3Url[0]);
            }
        }
        return $s3Url;
    }

    public static function extractRelativePathFromSignedS3Url(string $signedUrl)
    {
        $pattern = '/^https?:\/\/[a-z0-9.-]+\.s3(?:\.[a-z0-9-]+)?\.amazonaws\.com\/.*$/';
        if (preg_match($pattern, $signedUrl)) {
            $urlComponents = parse_url($signedUrl);

            return isset($urlComponents['path']) ? substr($urlComponents['path'], 1) : null;
        }
        return false;
    }

    public static function calculateHMacSHA256($data, $secretKey)
    {
        return hash_hmac('sha256', $data, $secretKey);
    }

    public static function calculateCredit($model_code, $input_token, $output_token, $second, $number_result)
    {
        $record = AIPricing::where('model_code', $model_code)->select('cost_normal', 'cost_input', 'cost_output', 'type')->first();
        $total_price = 0;
        switch ($record->type) {
            case 'text':
                $total_price = $input_token * $record->cost_input + $output_token * $record->cost_output;
                break;
            case 'text_to_video':
            case 'video':
            case 'image':
            case 'image_to_image':
            case 'image_to_video':
            case 'text_to_music':
            case 'music_to_text':
            case 'ai-audio':
            case 'flux-schnell':
            case 'consistent-character':
            case 'photo-beauty':
            case 'flux-1.1-pro':
            case 'latent-consistency':
            case 'kling':
            case 'aesthetic':
            case 'animate-diff':
            case 'animate':
            case 'elevenlab':
            case 'pebblely':
            case 'my_ai_image':
            case 'upscale-image':
            case 'image_combine':
                $total_price = $number_result * $record->cost_normal;
                break;
            case 'lipsync':
            case 'caption':
            case 'mc_virtual':
            case 'd-id':
            case 'creatomate':
            case 'gen3-alphaturboel_code':
            case 'heygen_photo_avatar':
            case 'heygen_video_avatar':
            case 'pictory_create_video':
            case 'pictory_create_content':
                $total_price = $second * $record->cost_normal;
                break;
            case 'generate-image-background':
                $total_price = $number_result * $record->cost_normal;
                break;
        }
        return ceil($total_price);
    }

    public static function runInCronJobs()
    {
        return app()->runningInConsole();
    }

    public static function convertHtmlToText($html = '')
    {
        $tableTexts = [];

        // Tìm và xử lý các bảng
        $html = preg_replace_callback('/<table[^>]*>([\s\S]*?)<\/table>/i', function ($matches) use (&$tableTexts) {
            $tableText = $this->convertTableToText($matches[0]);
            $tableTexts[] = $tableText;
            return "\n[[TABLE_" . (count($tableTexts) - 1) . "]]\n";
        }, $html);

        // Thay thế các thẻ HTML khác
        $html = preg_replace('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', "\n$1\n", $html);
        $html = preg_replace('/<p[^>]*>(.*?)<\/p>/i', "$1\n", $html);
        $html = preg_replace('/<\/?(ul|ol)[^>]*>/i', "\n", $html);
        $html = preg_replace('/<li[^>]*>(.*?)<\/li>/i', "- $1\n", $html);
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/?(thead|tbody|tfoot)[^>]*>/i', "\n", $html);
        $html = preg_replace('/<\/?[^>]+>/i', '', $html);

        // Xóa khoảng trắng dư thừa
        $html = trim(preg_replace('/\n\s*\n/', "\n", $html));

        // Thay thế văn bản bảng đã lưu lại
        foreach ($tableTexts as $index => $tableText) {
            $html = str_replace("[[TABLE_$index]]", $tableText, $html);
        }

        return $html;
    }

    function convertTableToText($tableHtml)
    {
        // Lấy hàng tiêu đề
        preg_match('/<tr[^>]*>([\s\S]*?)<\/tr>/i', $tableHtml, $headerRow);
        $headerRow = $headerRow[0] ?? '';
        preg_match_all('/<th[^>]*>(.*?)<\/th>/i', $headerRow, $headers);
        $headers = array_map(fn($header) => trim(strip_tags($header)), $headers[1]);
        $headerText = implode(' | ', $headers);

        // Lấy các hàng dữ liệu
        preg_match_all('/<tr[^>]*>([\s\S]*?)<\/tr>/i', $tableHtml, $dataRows);
        $dataRows = $dataRows[0] ?? [];
        $dataText = array_map(function ($row) {
            preg_match_all('/<td[^>]*>(.*?)<\/td>/i', $row, $cells);
            $cells = array_map(fn($cell) => trim(strip_tags($cell)), $cells[1]);
            return implode(' | ', $cells);
        }, $dataRows);

        // Kết hợp dữ liệu bảng
        $dataText = array_filter($dataText, fn($row) => $row !== '');
        return $headerText . " |\n" . implode("\n", $dataText) . " |";
    }
    public static function mapStatusFromHeygenToDiD(string $status): string {
        return $status === 'pending' ? 'created'
            : ($status === 'processing' ? 'started'
            : ($status === 'completed' ? 'done'
            : ($status === 'failed' ? 'error'
            : ($status === 'waiting' ? 'waiting'
            : 'error'))));
    }

    public static function getRemoteImage($url, $read_timeout = 30, $connection_timeout = 10, $retry_count = 3, $retry_delay = 100, array $retry_status_list = [500, 502, 503, 504], array $headers = [])
    {
        try {

            $response = Http::timeout($read_timeout)
                ->connectTimeout($connection_timeout)
                ->retry(
                    $retry_count,
                    $retry_delay,
                    function ($exception, $request) use ($retry_status_list) {
                        return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                            in_array(optional($exception->response)->status(), $retry_status_list, true);
                    }
                )
                ->withHeaders($headers)
                ->withOptions([
                    'verify' => false
                ])
                ->get($url);

            if ($response->successful()) {
                return $response->body();
            } else {
                throw new \Exception('Unable to fetch image from URL.');
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public static function convert_date_range_to_carbon($dateRange, $timezone = null)
    {
        [$from, $to] = $dateRange;
        if (empty($timezone)) {
            $from = (new Carbon($from))->startOfDay();
            $to = (new Carbon($to))->endOfDay();
        } else {
            $from = (new Carbon($from))->timezone($timezone)->startOfDay()->timezone('UTC');
            $to = (new Carbon($to))->timezone($timezone)->endOfDay()->timezone('UTC');
        }

        return [$from, $to];
    }

    public static function logException(\Exception $e): void
    {
        Log::error('Exception caught', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    public static function logInfo(string $message, $context = []): void
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? null;
        $file = $backtrace['file'] ?? 'unknown file';
        $line = $backtrace['line'] ?? 'unknown line';

        Log::info("[$file:$line] $message", $context);
    }

    public static function downloadAndUploadToS3($url, $s3Path)
    {
        // B1: Gửi HTTP request để lấy nội dung file
        $response = Http::get($url);

        if (!$response->successful()) {
            throw new \Exception('Không tải được file từ URL');
        }

        $fileContents = $response->body(); // raw binary
        // B2: Upload lên S3
        Storage::disk(config('constant.STORAGE_DISK'))->put($s3Path, $fileContents);

        try {
            return Storage::disk(config('constant.STORAGE_DISK'))->temporaryUrl(
                $s3Path,
                Carbon::now()->addMinutes(config('filesystems.disks.s3.time_presigned_url'))
            );
        } catch (\Exception $e) {
            Log::error('ERROR_S3_GET_URL:' . $e->getMessage());

            return null;
        }
    }

    public static function isJson($string)
    {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    public static function isProduction()
    {
        return config('constant.APP_ENV') == 'production';
    }
}
