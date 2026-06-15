<?php

namespace App\Enums;

enum LanguageseEnum: string
{
    case vi = 'Tiếng Việt';
    case en = 'Tiếng Anh';
    case zh = 'Tiếng Trung';
    case ja = 'Tiếng Nhật';
    case ko = 'Tiếng Hàn';
    case fr = 'Tiếng Pháp';
    case de = 'Tiếng Đức';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}
