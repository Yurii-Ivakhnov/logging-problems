<p align="center">
    <img src="https://avatars1.githubusercontent.com/u/33844443" height="100px">
    <h1 align="center">Logging Problems</h1>
    <br>
</p>

---
This package has:
 - Logging All Exceptions
 - Logging when Failed Job
 - Logging When Querying long
---
## Setting
### Check Provider

Check if `config/app.php` in `"providers"` exists below provider:

```php
Corpsoft\Logging\LoggingServiceProvider::class,
```

### Publish config file

Publish config file
```php
php artisan vendor:publish --tag=logging-problems
```
_If file not publish, run:_
```php
php artisan vendor:publish --tag=config
```

### Setting Slack webhook

Open link https://slack.com/apps/A0F7XDUAZ-incoming-webhooks, get webhook url, and put in _.env_

Add in _.env_ file 
>LOG_CS_SLACK_WEBHOOK_URL=

---

## Config File and .env variables

Possible _.env_ variables
```php
 LOG_CS_ENABLE_JOB_FAILED_TRACKING=
 LOG_CS_ENABLE_QUERYING_LONG_TRACKING=
 LOG_CS_ENABLE_GLOBAL_EXCEPTION_TRACKING=
 LOG_CS_MAX_QUERYING_TIME_TRACKING=
```
Here is a description of the _config\logging-problems.php_

```php
'enable_in_environment'  // Which environment enabled. Default enabled 'production'

'enable_job_failed_tracking' // Includes Tracking of Failing Job. Default true
'enable_querying_for_longer_time_tracking' // Includes Tracking of long query. Default true
'enable_global_exception_tracking' // Includes global tracking of 500 errors Default true

'max_querying_time_tracking' // The maximum query threshold at which this will be logged (in milliseconds), the default is 1000
```




# logging-problems
