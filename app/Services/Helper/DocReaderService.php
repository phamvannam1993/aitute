<?php

namespace App\Services\Helper;

use DOMDocument;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Shared\ZipArchive;
use Throwable;

class DocReaderService
{
    public function getText(string $filePath, string $extension): string
    {
        $type = match ($extension) {
            'doc' => 'MsDoc',
            'docx' => 'Word2007',
            default => null,
        };


        try {
            $reader = IOFactory::createReader($type);
        } catch (Throwable) {
            $reader = IOFactory::createReader('MsDoc');
        }
        $phpWord = $reader->load($filePath);
        $content = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $content .= $this->getWordText($element) . "\n";
            }
        }

        return $content;
    }

    private function getWordText($element): string
    {
        $result = '';
        if ($element instanceof AbstractContainer) {
            foreach ($element->getElements() as $subElement) {
                $result .= $this->getWordText($subElement);
            }
        } elseif ($element instanceof Text) {
            $result .= $element->getText();
        }
        // and so on for other element types (see src/PhpWord/Element)

        return $result;
    }

    /**
     * @throws Exception
     */
    public function getPageCount(string $filePath): int
    {
        try {
            $zip = new ZipArchive();
            if ($zip->open($filePath) && $zip->locateName("docProps/app.xml")) {
                preg_match("/<Pages>(.*)<\/Pages>/", $zip->getFromName("docProps/app.xml"), $var);
                $zip->close();

                return (int)$var[1];
            }

            return $this->convertToPDFAndCountPages($filePath);
        } catch (Throwable) {
            return $this->convertToPDFAndCountPages($filePath);
        }
    }

    /**
     * @throws Exception
     */
    private function convertToPDFAndCountPages(string $filePath): int
    {
        $pdfPath = $this->convertToPDF($filePath);
        $count = app(PDFReaderService::class)->getPageCount($pdfPath);
        unlink($pdfPath);

        return $count;
    }

    /**
     * @throws Exception
     */
    private function convertToPDF(string $filePath): string
    {
        try {
            $phpWord = IOFactory::load($filePath);
        } catch (Throwable) {
            $phpWord = IOFactory::load($filePath, 'MsDoc');
        }
        Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
        Settings::setPdfRendererName('DomPDF');
        $xmlWriter = IOFactory::createWriter($phpWord, 'PDF');
        $tempPath = storage_path('app/public/slides/' . uniqid('', true) . '.pdf');
        $xmlWriter->save($tempPath);

        return $tempPath;
    }

    /**
    * @throws Exception
    */
    public function exportToDoc(string $content): false|string
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        Html::addHtml($section, $this->convertToHtml($content), true, false);

        $writer = IOFactory::createWriter($phpWord);
        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $writer->save($tempFile);

        return $tempFile;
    }

    /**
    * @param $html
    * @return array|string|null
     */
    private function convertToHtml($html):array|string|null
    {
        // Encode UTF-8
        $html = mb_convert_encoding($html, "HTML-ENTITIES", "UTF-8");

        // Add document for html
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $html = $doc->saveHTML();

        // replace tag html invalid
        $html = preg_replace('/<hr\s*\/?>/', '<hr />', $html);
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        return preg_replace('/<br\s*\/?>/', '<br />', $html);
    }
}
