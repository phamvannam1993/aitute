<?php

namespace App\Helper;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Reader\Word2007;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Shared\ZipArchive;

class DocReaderService
{
    public function getText(string $filePath, string $extension): string
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    if(is_array($element->getText())) {
                        $text .= $element->getText()[0];
                    } else {
                        $text .= $element->getText();
                    }
                }
            }
        }
        return $text;
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
        $zip = new ZipArchive();
        $zip->open($filePath);
        preg_match("/<Pages>(.*)<\/Pages>/", $zip->getFromName("docProps/app.xml"), $var);
        $zip->close();

        return (int) $var[1];
    }
}
