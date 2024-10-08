<?php

namespace App\Enums;

enum ComplexityEnum: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    const EASY_TEXT = 'Легкая';
    const MEDIUM_TEXT = 'Средняя';
    const HARD_TEXT = 'Тяжелая';

    public static function getAllWithText(): array
    {
        return [
            self::EASY->value => self::EASY_TEXT,
            self::MEDIUM->value => self::MEDIUM_TEXT,
            self::HARD->value => self::HARD_TEXT,
        ];
    }

    public static function getOneWithText(string $complexity): string|null
    {
        return match ($complexity) {
            self::EASY->value => self::EASY_TEXT,
            self::MEDIUM->value => self::MEDIUM_TEXT,
            self::HARD->value => self::HARD_TEXT,

            default => null,
        };
    }
}
