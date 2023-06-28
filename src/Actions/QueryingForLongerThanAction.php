<?php

namespace Corpsoft\Logging\Actions;

use Corpsoft\Logging\Helpers\GetFilteredAndFormattedStack;
use Corpsoft\Logging\Notifications\LongQueryingNotification;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class QueryingForLongerThanAction
{
    public function __invoke(): void
    {
        if (config('logging-problems')['enable_querying_for_longer_time_tracking']) {
            $maxQueryingTimeTracking = config('logging-problems')['max_querying_time_tracking'] ?? 1000;

            DB::whenQueryingForLongerThan($maxQueryingTimeTracking, function (Connection $connection, QueryExecuted $event) {
                Notification::route('slack', config('logging-problems')['log_slack_webhook_url'])
                    ->notify(new LongQueryingNotification($this->getAttachmentFields($connection, $event)));
            });
        }
    }

    /**
     * Get Attachments for message
     * @param Connection $connection
     * @param QueryExecuted $event
     * @return array
     */
    private function getAttachmentFields(Connection $connection, QueryExecuted $event): array
    {
        return [
            'Sql ' => $this->getSql($event),
            'Time' => $this->getTime($event),
            'SERVER_INFO' => $this->getServerInfo($connection),
            'Database' => $this->getDatabaseName($connection),
            'Trace' => $this->getTraceAsString()
        ];
    }

    /**
     * @param QueryExecuted $event
     * @return string
     */
    private function getSql(QueryExecuted $event): string
    {
        return "`" . str_replace('`', "'", $event->sql) . "`";
    }

    /**
     * @param QueryExecuted $event
     * @return string
     */
    private function getTime(QueryExecuted $event): string
    {
        return ($event->time / 1000) . ' sec';
    }

    /**
     * @param Connection $connection
     * @return string
     */
    private function getServerInfo(Connection $connection): string
    {
        return $connection
            ->getPdo()
            ->getAttribute(\PDO::ATTR_SERVER_INFO);
    }

    /**
     * @param Connection $connection
     * @return string
     */
    private function getDatabaseName(Connection $connection): string
    {
        return $connection->getDatabaseName();
    }

    private function getTraceAsString(): string
    {
        $callStack = $this->getCallStack();

        return implode(" \n \n ", array_values($callStack));
    }

    /**
     * Get Format Call Stack
     * @return array
     */
    private function getCallStack(): array
    {
        $getFilteredStack = new GetFilteredAndFormattedStack();
        $callStack = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);

        return $getFilteredStack($callStack);
    }
}
