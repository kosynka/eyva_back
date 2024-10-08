<?php

namespace App\Enums;

enum FileEnum: int
{
    case PHOTO_KEY = 1;
    case VIDEO_KEY = 2;

    const PHOTO_NAME = 'photo';
    const VIDEO_NAME = 'video';

    const PHOTO_TEXT = 'фото';
    const VIDEO_TEXT = 'видео';

    public static function getType(int $type): string
    {
        return match ($type) {
            self::PHOTO_KEY->value => self::PHOTO_NAME,
            self::VIDEO_KEY->value => self::VIDEO_NAME,
        };
    }

    public static function getTypes(): array
    {
        return [
            self::PHOTO_KEY->value => self::PHOTO_NAME,
            self::VIDEO_KEY->value => self::VIDEO_NAME,
        ];
    }

    public static function getTypesWithText(): array
    {
        return [
            self::PHOTO_KEY->value => self::PHOTO_TEXT,
            self::VIDEO_KEY->value => self::VIDEO_TEXT,
        ];
    }

    public static function getTypeWithText(int $type): string
    {
        return match ($type) {
            self::PHOTO_KEY->value => self::PHOTO_TEXT,
            self::VIDEO_KEY->value => self::VIDEO_TEXT,
        };
    }
}
