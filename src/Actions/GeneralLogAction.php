<?php

namespace Corpsoft\Logging\Actions;

use Corpsoft\Logging\Helpers\GetFilteredAndFormattedStack;
use Corpsoft\Logging\Notifications\GeneralLogNotification;
use Illuminate\Support\Facades\Notification;
use Throwable;

class GeneralLogAction
{
    /**
     * @param Throwable $e
     * @return void
     */
    public function __invoke(Throwable $e): void
    {
        Notification::route('slack', config('logging-problems')['log_slack_webhook_url'])
            ->notify(new GeneralLogNotification($e->getMessage(), $this->getAttachmentFields($e)));
    }

    /**
     * @param Throwable $e
     * @return array
     */
    private function getAttachmentFields(Throwable $e): array
    {
        return [
            'File' => $e->getFile(),
            'Line' => $e->getLine(),
            'Trace' => $this->getFormatTraceAsString($e->getTrace()),
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
