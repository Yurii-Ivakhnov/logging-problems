<?php

namespace Corpsoft\Logging\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Events\JobFailed;

class GeneralLogNotification extends Notification
{
    use Queueable;

    protected string $message;
    protected array $attachmentFields;

    /**
     * @param string $message
     * @param array $attachmentFields
     */
    public function __construct(string $message, array $attachmentFields)
    {
        $this->message = $message;
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
            ->from('500 Error', ':sad1:')
            ->error()
            ->content(ucfirst(config('app.env')) . ' - 500 error - '.config('app.name') . "\n" . $this->message)
            ->attachment(function (SlackAttachment $attachment) use ($notifiable) {
                $attachment->fields($this->attachmentFields);
            });
    }
}
