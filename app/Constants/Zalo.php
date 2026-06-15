<?php

namespace App\Constants;

class Zalo
{
    const ACCESS_TOKEN_LIFETIME_DAY = 1; //max: 25 hours
    const REFRESH_TOKEN_LIFETIME_DAY = 60; //max: 90 days
    const DAYS_REMAINING_TO_EXPIRE = 30;
    const EVENT_NAME_USER_SEND_TEXT = 'user_send_text';
    const EVENT_NAME_USER_SEND_STICKER = 'user_send_sticker';
}
