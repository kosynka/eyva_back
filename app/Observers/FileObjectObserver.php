<?php

namespace App\Observers;

use App\Enums\FileEnum;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\Storage;

class FileObjectObserver
{
    public function creating($model): void
    {
        // if ($model->needPreview() && $model->getFileTypeAttribute() === FileEnum::VIDEO_NAME) {
        //     $preview = $this->generatePreviewForVideo($model);
        //     $model->attributes[$model->previewAttributeName] = $preview;
        //     $model->saveQuetly();
        // }
    }

    public function updated($model): void
    {
        // if ($model->needPreview() && $model->getFileTypeAttribute() === FileEnum::VIDEO_NAME) {
        //     $preview = $this->generatePreviewForVideo($model);
        //     $model->attributes[$model->previewAttributeName] = $preview;
        //     $model->saveQuetly();
        // }
    }
}
