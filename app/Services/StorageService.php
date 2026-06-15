<?php

namespace App\Services;

use Aws\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;


class StorageService
{
    public function __construct() {}
    public function putToS3($file, $folderName = 'media')
    {
        set_time_limit(1200);
        $path = $folderName . '/' . time() . '_' .
            str(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            ->slug('_') . '.' .
            $file->getClientOriginalExtension();
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => config('filesystems.disks.s3.region'),
        ]);
        $contents = fopen($file, 'rb');
        $uploader = new MultipartUploader($s3, $contents, [
            'bucket' => config('filesystems.disks.s3.bucket'),
            'key'    => $path,
        ]);

        try {
            $uploader->upload();
        } catch (MultipartUploadException $e) {
            return $e->getMessage();
        }

        return [
            'path' => $path,
            'url' => Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60)),
        ];
    }
    public function getSizeS3($filePath)
    {
        return Storage::disk('s3')->size($filePath);
    }
    public function upload_to_s3($file, $key)
    {
        set_time_limit(1200);
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => config('filesystems.disks.s3.region'),
        ]);
        $contents = fopen($file, 'rb');
        $uploader = new MultipartUploader($s3, $contents, [
            'bucket' => config('filesystems.disks.s3.bucket'),
            'key'    => $key,
        ]);
        try {
            $result = $uploader->upload();
        } catch (MultipartUploadException $e) {
            return $e->getMessage();
        }
        return [
            'path' => $key,
            'url' => Storage::disk('s3')->temporaryUrl($key, now()->addMinutes(5)),
        ];
    }

    public function deleteFromS3($filePath)
    {
        return Storage::disk('s3')->delete($filePath);
    }

    public function putUrlToS3($url, $folderName = 'media')
    {
        $options = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $context = stream_context_create($options);

        $imageContent = file_get_contents($url);

        $basename = pathinfo($url, PATHINFO_BASENAME);
        $path = $folderName . '/' . time() . '_' . str($basename)->slug('_');
        Storage::disk('s3')->put($path, $imageContent);

        return [
            'path' => $path,
            'url' => Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5)),
        ];
    }

    public function putContentToS3($filename, $imageContent, $folderName = 'media')
    {
        $basename = pathinfo($filename, PATHINFO_BASENAME);
        $path = $folderName . '/' . time() . '_' . str($basename)->slug('_');
        Storage::disk('s3')->put($path, $imageContent);

        return [
            'path' => $path,
            'url' => Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5)),
        ];
    }
}
