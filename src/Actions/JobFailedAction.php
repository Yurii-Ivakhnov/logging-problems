<?php

namespace Corpsoft\Logging\Actions;

use Corpsoft\Logging\Helpers\GetFilteredAndFormattedStack;
use Corpsoft\Logging\Notifications\FailedJobNotification;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

class JobFailedAction
{
    public function __invoke(): void
    {
        if (config('logging-problems')['enable_job_failed_tracking']) {
            Queue::failing(function (JobFailed $event) {
                Notification::route('slack', config('logging-problems')['log_slack_webhook_url'])
                    ->notify(new FailedJobNotification($this->getAttachmentFields($event)));
            });
        }
    }

    /**
     * @param JobFailed $event
     * @return array
     */
    private function getAttachmentFields(JobFailed $event): array
    {
        return [
            'Message' => $event->exception->getMessage(),
            'Job class' => $event->job->resolveName(),
            'Trace' => $this->getFormatTraceAsString($event->exception->getTrace()),
            'Job body' => $event->job->getRawBody(),
        ];
    }

    /**
     * @param array $trace
     * @return string
     */
    private function getFormatTraceAsString(array $trace): string
    {
        $getFilteredStack = new GetFilteredAndFormattedStack();
        $callStack = $getFilteredStack($trace);

        return implode(" \n \n ", array_values($callStack));
    }
}
