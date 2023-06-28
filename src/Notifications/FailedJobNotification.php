<?php

namespace Corpsoft\Logging\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Events\JobFailed;

class FailedJobNotification extends Notification
{
    use Queueable;

    protected array $attachmentFields;

    /**
     * @param array $attachmentFields
     */
    public function __construct(array $attachmentFields)
    {
        $this->attachmentFields = $attachmentFields;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable): SlackMessage
    {
        return (new SlackMessage)
            ->from('Job Failed', ':sadcat:')
            ->error()
            ->content(ucfirst(config('app.env')) . '- A job failed at '.config('app.name'))
            ->attachment(function (SlackAttachment $attachment) use ($notifiable) {
                $attachment->fields($this->attachmentFields);
            });
    }
}
