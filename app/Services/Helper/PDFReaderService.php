<?php

namespace App\Services\Helper;
use App\Exceptions\InputException;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use Spatie\PdfToText\Exceptions\PdfNotFound;
use Spatie\PdfToText\Pdf;

class PDFReaderService
{
    /**
     * @throws PdfNotFound
     */
    public function extractText(string $filePath): string
    {
        $text = (new Pdf(config('services.pdf_to_text.binary_path')))
            ->setPdf($filePath)
            ->text();

        return $text;
    }

    /**
     * @throws PdfParserException
     */
    public function getPageCount(string $filePath): int
    {
        return (new Fpdi())->setSourceFile($filePath);
    }
}
