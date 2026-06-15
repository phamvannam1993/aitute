<?php

namespace App\Enums;

use App\Models\FacebookFanpage;

enum SocialPostTypeEnum: string
{
    case FacebookFanpage = FacebookFanpage::class;

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
        return array_combine(self::names(), self::values());
    }
}
