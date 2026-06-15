<?php

namespace App\Helper;
use Illuminate\Support\Facades\Log;
use Spatie\PdfToText\Exceptions\PdfNotFound;
use Spatie\PdfToText\Pdf;
use Symfony\Component\Process\Process;

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


    public function getTextFromPdf($filePath, $pages = 10)
    {
        try {
            $process = new Process([
                'pdftotext',
                '-f', '1',
                '-l', $pages,
                $filePath,
                '-'
            ]);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }

            return $process->getOutput();
        } catch (\Exception $e) {
            Log::error('Unexpected Exception: ' . $e->getMessage());
        }
    }

    public function checkPageLimit($filePath, $maxPages = 10)
    {
        try {
            $process = new Process([
                'pdfinfo',
                $filePath
            ]);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }

            $output = $process->getOutput();
            preg_match('/Pages:\s+(\d+)/', $output, $matches);
            $numPages = isset($matches[1]) ? (int)$matches[1] : 0;

            return $numPages > $maxPages;
        } catch (\Exception $e) {
            Log::error('Error while checking page limit: ' . $e->getMessage());
            return false;
        }
    }
}
