<?php

namespace TempestTools\Raven\Laravel\Orm\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use TempestTools\Raven\Laravel\Constants\ViaTypesConstants;
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
     * @var EntityContract
     */
    protected $entity;

    /**
     * Create a new notification instance.
     *
     * @param EntityContract $entity
     */
    public function __construct(EntityContract $entity)
    {
        $this->setEntity($entity);
    }

    /**
     * A method to be overridden add additional stuff to a mail message (such as the sort of things that generally be handled by a view).
     * @param MailMessage $mailMessage
     * @return MailMessage
     */
    protected function addToMailMessage(MailMessage $mailMessage):MailMessage {
        return $mailMessage;
    }

    /**
     * A method to be overridden add additional stuff to a nexmo message (such as the sort of things that generally be handled by a view).
     * @param NexmoMessage $nexmoMessage
     * @return NexmoMessage
     */
    protected function addToNexmoMessage(NexmoMessage $nexmoMessage):NexmoMessage {
        return $nexmoMessage;
    }

    /**
     * A method to be overridden add additional stuff to a slack message (such as the sort of things that generally be handled by a view).
     * @param SlackMessage $slackMessage
     * @return SlackMessage
     */
    protected function addToSlackMessage(SlackMessage $slackMessage):SlackMessage {
        return $slackMessage;
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
        $mailSettings = $this->getVia()[ViaTypesConstants::MAIL] ?? [];
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
     * Get the mail representation of the notification. If there is a view set on the notification it will apply it to the email, and pass in the entity->toArray as it's view data
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack(EntityContract $notifiable):SlackMessage
    {
        $slackMessage = new SlackMessage();
        $slackSettings = $this->getVia()[ViaTypesConstants::SLACK] ?? [];
        foreach ($slackSettings as $key => $value) {
            $slackMessage->$key($value);
        }
        $slackMessage = $this->addToSlackMessage($slackMessage);
        return $slackMessage;
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return NexmoMessage
     */
    public function toNexmo(EntityContract $notifiable)
    {
        $nexmoMessage = new NexmoMessage();
        $nexmoSettings = $this->getVia()[ViaTypesConstants::NEXMO] ?? [];
        foreach ($nexmoSettings as $key => $value) {
            $nexmoMessage->$key($value);
        }
        $nexmoMessage = $this->addToNexmoMessage($nexmoMessage);

        return $nexmoMessage;
    }

    /**
     * Sets the broadcast channel for the notification. By default will return like: {notifiable}.{id}. Such as: 'App.User.1'
     * @return string
     * TODO: Test this when we upgrade laravel all the way
     */
    public function toBroadcast ():string {
        if (isset($this->getVia()[ViaTypesConstants::BROADCAST]) && $this->getVia()[ViaTypesConstants::BROADCAST]['channel']) {
            return $this->getVia()[ViaTypesConstants::BROADCAST]['channel'];
        }

        return preg_replace('/\/', '.', \get_class($this->getEntity())) . '.' . $this->getEntity()->getId();
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

    /**
     * @return EntityContract
     */
    public function getEntity(): EntityContract
    {
        return $this->entity;
    }

    /**
     * @param EntityContract $entity
     */
    public function setEntity(EntityContract $entity): void
    {
        $this->entity = $entity;
    }
}