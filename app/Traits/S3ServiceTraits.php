<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait S3ServiceTraits
{
    /**
     * Upload file role private.
     *
     * @param $filePath
     * @param string $path
     * @return boolean|string
     */
    public function uploadFile($filePath, string $pathTmp)
    {
        try {
            $s3 = Storage::disk('s3');

            $s3->put(
                $filePath, // Đặt đường dẫn trên S3
                file_get_contents(storage_path('app/public/' .$pathTmp)) // Lấy dữ liệu từ tệp lưu trữ
            );
            return Storage::disk('s3')->url($filePath);

        } catch (Exception $e) {
            Log::error('[ERROR_S3_UPLOAD] =>' . $e->getMessage());

            return false;
        }
    }

    /**
     * Gennerate presigned url has expried time.
     *
     * @param string $name
     * @param string $path
     *
     * @return null|string
     */
    public function getUrl(string $path)
    {
        try {
            return Storage::disk('s3')->temporaryUrl(
                $path,
                Carbon::now()->addMinutes(config('filesystems.disks.s3.time_presigned_url'))
            );
        } catch (Exception $e) {
            Log::error('ERROR_S3_GET_URL:' . $e->getMessage());

            return null;
        }
    }

    /**
     * Delete image.
     *
     * @param string $name
     * @param string $path
     *
     * @return boolean
     */
    public function deleteImage(string $name, string $path): bool
    {
        try {
            $key = $this->getUploadKey($path, $name);

            return Storage::disk('s3')->delete($key);
        } catch (Exception $e) {
            Log::error('[ERROR_S3_DELETE_IMAGE] =>' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get upload key to s3.
     *
     * @param string $path path.
     * @param string $name name.
     *
     * @return string
     */
    public function getUploadKey(string $path, string $name): string
    {
        return sprintf('%s/%s', $path, $name);
    }

    /**
     * Create s3 pre-signed url.
     *
     * @param string $name
     * @param string $path
     * @return null|string
     */
    public function getPreSigned(string $tmpFilePath): string
    {
        $client = Storage::disk('s3')->getClient();
        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $tmpFilePath,
        ]);

        $request = $client->createPresignedRequest($command, '+20 minutes');

        return (string) $request->getUri();
    }

    public function getObjectFile(string $path): string
    {
        $s3 = Storage::disk('s3');
        $client = $s3->getClient();
        $expiry = "+10 minutes";

        $command = $client->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $path,
        ]);

        $request = $client->createPresignedRequest($command, $expiry);

        return (string) $request->getUri();
    }

    public function getFile(string $path): string
    {
        $s3 = Storage::disk('s3');
        $client = $s3->getClient();
        $expiry = "+10 minutes";

        $command = $client->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $path,
        ]);
        return (string) \Aws\serialize ($command)->getUri();
    }

    public function deleteFolder($tmpFilePath)
    {
        $s3 = Storage::disk('s3');
        // Remove property photos from AWS S3
        if ($s3->exists($tmpFilePath)) {
            Storage::disk('s3')->deleteDirectory($tmpFilePath);
        }
    }

    /**
     * Upload file to s3
     *
     * @param object $file
     * @param string $path
     *
     * @return boolean|string
     */
    public function uploadToS3(string $path, object $file)
    {
        try {
            $path = Storage::disk('s3')->put($path, $file);
            $uri = Storage::disk('s3')->url($path);
            return $path;
        } catch (Exception $e) {
            Log::error('[ERROR_S3_UPLOAD_IMAGE] =>' . $e->getMessage());

            return false;
        }
    }

    /**
     * get file name.
     *
     * @param $file
     *
     * @return boolean|string
     */
    public function getFileName($file)
    {
        try {
            $tmp_extension = $file->getClientOriginalExtension();
            $format_files = ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'tif', 'svg', 'pdf'];
            if (!(in_array($tmp_extension, $format_files))) {
                return false;
            }

            preg_match('/.([0-9]+) /', microtime(), $m);

            return sprintf('img%s%s.%s', date('YmdHis'), $m[1], $tmp_extension);
        } catch (Exception $e) {
            return false;
        }
    }
}
