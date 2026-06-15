<?php

namespace App\Rules;

use App\Services\Helper\DocReaderService;
use App\Services\Helper\PDFReaderService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxFilePageRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $extension = $value->getClientOriginalExtension();
        $pageCount = 0;
        if ($extension === 'doc' || $extension === 'docx') {
            $pageCount = app(DocReaderService::class)->getPageCount($value->getRealPath());
        }

        if ($extension === 'pdf') {
            $pageCount = app(PDFReaderService::class)->getPageCount($value->getRealPath());
        }

        $maxPage = config('slide.max_source_file_page');
        if ($pageCount > $maxPage) {
            $fail("Số trang của file phải từ 1 đến {$maxPage} trang");
        }
    }
}
