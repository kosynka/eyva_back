<?php

namespace App\Helpers;

trait AmountConverter
{
    private const EYV_TO_KZT_RATE = 400;
    private const KZT_TO_EYV_RATE = 1 / self::EYV_TO_KZT_RATE;

    private const KZT_TO_KZTCOIN_RATE = 100;
    private const KZTCOIN_TO_KZT_RATE = 1 / self::KZT_TO_KZTCOIN_RATE;

    public function convertEyvToKzt(int $eyvAmount): int
    {
        if ($eyvAmount <= 0) {
            return 0;
        }

        return $eyvAmount * self::EYV_TO_KZT_RATE;
    }

    public function convertKztToEyv(int $kztAmount): int
    {
        if ($kztAmount <= 0) {
            return 0;
        }

        return $kztAmount * self::KZT_TO_EYV_RATE;
    }

    public function convertKztToKztCoin(int $kztAmount): int
    {
        if ($kztAmount <= 0) {
            return 0;
        }

        return $kztAmount * self::KZT_TO_KZTCOIN_RATE;
    }

    public function convertKztCoinToKzt(int $coinAmount): int
    {
        if ($coinAmount <= 0) {
            return 0;
        }

        return round($coinAmount * self::KZTCOIN_TO_KZT_RATE);
    }
}
