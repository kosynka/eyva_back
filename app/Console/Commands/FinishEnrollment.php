<?php

namespace App\Console\Commands;

use App\Models\UserServiceSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class FinishEnrollment extends Command
{
    protected $signature = 'enrollment:finish';
    protected $isolated = true;
    protected $description = 'Set finished status to all enrollements that are not finished yet.';

    public function handle()
    {
        $enrollments = UserServiceSchedule::with(['schedule.service'])
            ->where('status', UserServiceSchedule::STATUS_ENROLLED)
            ->whereHas('schedule', function ($query) {
                $query->whereRaw('(start_date + start_time::interval) <= ?', [now()->toDateTimeString()]);
            })
            ->get();

        echo 'Enrollments Start for checking: ' . $enrollments->count() . PHP_EOL;
        Log::info(PHP_EOL . 'Enrollments Start for checking: ' . $enrollments->count());

        foreach ($enrollments as $enrollment) {
            /** @var \Carbon\Carbon $scheduleEndTime */
            $scheduleEndTime = $enrollment->schedule->start_date_time->addMinutes($enrollment->schedule->service->duration);

            if ($scheduleEndTime->isBefore(now())) {
                $enrollment->status = UserServiceSchedule::STATUS_FINISHED;
                $enrollment->save();

                Log::info("Enrollment #$enrollment->id was finished");
            }
        }

        echo 'Enrollments End for checking' . PHP_EOL;
        Log::info("Enrollments End for checking \n");

        return SymfonyCommand::SUCCESS;
    }
}
