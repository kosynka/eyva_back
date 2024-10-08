<?php

namespace App\Enums;

enum BuyableStatusEnum: string
{
    case DISABLED = 'disabled';
    case ENABLED = 'enabled';

    const DISABLED_KEY = 1;
    const ENABLED_KEY = 2;

    const DISABLED_TEXT = 'Скрыт';
    const ENABLED_TEXT = 'Опубликован';

    public static function getStatus(int $type): string
    {
        return match ($type) {
            self::DISABLED_KEY => self::DISABLED->value,
            self::ENABLED_KEY => self::ENABLED->value,
            default => 0,
        };
    }

    public static function getStatusText(int $type): string
    {
        return match ($type) {
            self::DISABLED_KEY => self::DISABLED_TEXT,
            self::ENABLED_KEY => self::ENABLED_TEXT,
            default => 0,
        };
    }

    public static function getStatuses(): array
    {
        return [
            self::DISABLED_KEY => self::DISABLED_TEXT,
            self::ENABLED_KEY => self::ENABLED_TEXT,
        ];
    }
}
