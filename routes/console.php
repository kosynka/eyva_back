<?php

use App\Console\Commands\FinishEnrollment;
use Illuminate\Support\Facades\Schedule;

Schedule::command(FinishEnrollment::class)
    ->appendOutputTo(storage_path('logs/schedule.log'))
    ->everyFiveMinutes();
