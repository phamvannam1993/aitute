<?php

namespace App\Enums;

enum SocialPostEnum: string
{
    case PUBLISHED = 'Published';
    case SCHEDULED = 'Scheduled';
    case FAILED = 'Failed';
    case RETRYING = 'Retrying';
}
