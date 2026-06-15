<?php

namespace App\Constants;

class PaymentMethod
{
    // ID
    const BANK_TRANSFER_ID = 1;
    const QR_MOMO = 2;
    const ATM_MOMO = 3;
    const QR_VNPAY = 4;
    const ATM_VNPAY = 5;
    const ALEPAY = 6;

    const BANK_TRANSFER_ID_NAME = 'chuyển khoản';
    const QR_MOMO_NAME = 'momo';
    const ATM_MOMO_NAME = 'momo';
    const QR_VNPAY_NAME = 'vnpay';
    const ATM_VNPAY_NAME = 'vnpay';
    const ALEPAY_NAME = 'alepay';

    public static function listPaymentMethod()
    {
        return [
            self::BANK_TRANSFER_ID => self::BANK_TRANSFER_ID_NAME,
            self::QR_MOMO => self::QR_MOMO_NAME,
            self::ATM_MOMO => self::ATM_MOMO_NAME,
            self::QR_VNPAY => self::QR_VNPAY_NAME,
            self::ATM_VNPAY => self::ATM_VNPAY_NAME,
            self::ALEPAY => self::ALEPAY_NAME
        ];
    }
}
