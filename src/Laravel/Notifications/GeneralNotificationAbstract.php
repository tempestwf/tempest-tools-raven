<?php

namespace TempestTools\Raven\Laravel\Orm\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use TempestTools\Scribe\Contracts\Orm\EntityContract;

class GeneralNotificationAbstract extends Notification
{
    use Queueable;
    /**
     * @var array
     */
    protected $via;

    /**
     * @var array the settings to pass to the to array method of the entities. See the scribe example for toArray settings that can be put on a controller.
     */
    protected $toArraySettings = [];

    /**
     * @var string
     */
    protected $mailView;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  EntityContract  $notifiable
     * @return array
     */
    public function via(EntityContract $notifiable):array
    {
        return array_keys($this->getVia());
    }

    /**
     * Get the mail representation of the notification. If there is a view set on the notification it will apply it to the email, and pass in the entity->toArray as it's view data
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(EntityContract $notifiable):MailMessage
    {
        $mailMessage = new MailMessage();
        $mailSettings = $this->getVia()['mail'] ?? [];
        foreach ($mailSettings as $key => $value) {
            $mailMessage->$key($value);
        }
        $mailMessage = $this->addToMailMessage($mailMessage);
        if ($this->getMailView() !== null) {
            $mailMessage->view(
                $this->getMailView(),
                $notifiable->toArray($this->getToArraySettings())
            );
        }
        return $mailMessage;
    }

    /**
     * A method to be overriden add additional stuff to a mail message (such as the sort of things that generally be handled by a view).
     * @param MailMessage $mailMessage
     * @return MailMessage
     */
    protected function addToMailMessage(MailMessage $mailMessage):MailMessage {
        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(EntityContract $notifiable):array
    {
        return $notifiable->toArray($this->getToArraySettings());
    }

    /**
     * @return array
     */
    public function getVia(): array
    {
        return $this->via;
    }

    /**
     * @param array $via
     */
    public function setVia(array $via): void
    {
        $this->via = $via;
    }

    /**
     * @return array
     */
    public function getToArraySettings(): array
    {
        return $this->toArraySettings;
    }

    /**
     * @return mixed
     */
    public function getMailView()
    {
        return $this->mailView;
    }
}
