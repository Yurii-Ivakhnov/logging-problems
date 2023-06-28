<?php

namespace Corpsoft\Logging\Exceptions;

use Corpsoft\Logging\Actions\GeneralLogAction;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use \Throwable;

class LoggingExceptionHandler extends ExceptionHandler
{

    protected $dontReport = [];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    /**
     * @param $request
     * @param Throwable $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        $response = parent::render($request, $e);
        $logConfig = config('logging-problems');


        if ($logConfig['log_slack_webhook_url']
            && $logConfig['enable_global_exception_tracking']
            && app()->environment($logConfig['enable_in_environment'])
            && $response->status() == 500)
        {
            $this->logError($e);
        }

        return $response;
    }

    /**
     * @param Throwable $e
     * @return void
     */
    private function logError(Throwable $e): void
    {
        $action = new GeneralLogAction();
        $action($e);
    }
}
