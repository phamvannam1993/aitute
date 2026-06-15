<?php

namespace App\Services;

use App\Helper\AudioReaderService;
use App\Helper\DocReaderService;
use App\Helper\ImageReaderService;
use App\Helper\PDFReaderService;
use Exception;
use Illuminate\Http\UploadedFile;
use Spatie\PdfToText\Exceptions\PdfNotFound;

class DocumentReaderService
{
    public function __construct(
        private PDFReaderService $pdfReaderService,
        private DocReaderService $docReaderService,
        private ImageReaderService $imageReaderService,
        private AudioReaderService $audioReaderService,
        private AudioService $audioService
    )
    {
    }

    /**
     * @throws PdfNotFound
     */
    public function readContent(UploadedFile $file, $languageKey = 'vi'): string
    {
        $content = '';
        $extension = $file->getClientOriginalExtension();
        if ($extension === 'pdf') {
            $content = $this->pdfReaderService->extractText($file->getRealPath());
        } else if ($extension === 'doc' || $extension === 'docx' || $extension === 'dotx') {
            $content = $this->docReaderService->getText($file->getRealPath(), $extension);
        } else if (in_array($extension, ['png', 'jpg', 'jpeg', 'webp'])) {
            $content = $this->imageReaderService->extractText($file->getRealPath());
        } else if (in_array($extension, ['mp3', 'wav', 'webm'])) {
            if ($extension === 'webm') {
                $audioName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $convertedFilePath = $this->audioService->convertWebmToMp3($file->getRealPath(), $extension, $audioName);
                $content = $this->audioReaderService->extractAudioToText($convertedFilePath, $languageKey);
            } else {
                $content = $this->audioReaderService->extractAudioToText($file->getRealPath(), $languageKey);
            }
        } else {
            throw new Exception('Unsupported file type');
        }

        if (!$content) {
            throw new Exception('Không thể đọc nội dung từ file đã chọn');
        }

        return $content;
    }

    public function readContentPdfLimitPage(UploadedFile $file, $page = 10, $languageKey = 'vi'): string
    {
        $content = $this->pdfReaderService->getTextFromPdf($file->getRealPath(), $page);
        if (!$content) {
            throw new Exception('Không thể đọc nội dung từ file đã chọn');
        }

        return $content;
    }

    public function checkPageLimit(UploadedFile $file, $page = 10) {
         return  $this->pdfReaderService->checkPageLimit($file->getRealPath(), $page);
    }
}
