<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait ModelHelper
{
    public static function getModelClassFromTable(string $tableName): ?string
    {
        $modelDirectory = app_path('Models');

        $files = File::allFiles($modelDirectory);

        foreach ($files as $file) {
            $modelClass = self::resolveModelClassFromFile($file->getPathname());

            if ($modelClass && (new $modelClass())->getTable() === $tableName) {
                return $modelClass;
            }
        }

        return null;
    }

    protected static function resolveModelClassFromFile(string $filePath): ?string
    {
        $relativePath = Str::after($filePath, app_path() . DIRECTORY_SEPARATOR);
        $namespacePath = str_replace(
            [DIRECTORY_SEPARATOR, '.php'],
            ['\\', ''],
            $relativePath
        );

        $fullClassName = "App\\$namespacePath";

        return class_exists($fullClassName) ? $fullClassName : null;
    }
}
