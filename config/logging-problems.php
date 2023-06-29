<?php

return [
    /**
     * Global configs
     */
    'log_slack_webhook_url' => env('LOG_CS_SLACK_WEBHOOK_URL', false),
    'enable_in_environment' => [
        'production',
//        'local','
//        'staging',
    ],

    /**
     * Enable Actions
     */
    'enable_job_failed_tracking' => env('LOG_CS_ENABLE_JOB_FAILED_TRACKING', true),
    'enable_querying_for_longer_time_tracking' => env('LOG_CS_ENABLE_QUERYING_LONG_TRACKING', true),
    'enable_global_exception_tracking' => env('LOG_CS_ENABLE_GLOBAL_EXCEPTION_TRACKING', true),

    /**
     * Action configs
     */
    'max_querying_time_tracking' => env('LOG_CS_MAX_QUERYING_TIME_TRACKING', 1000),
];
