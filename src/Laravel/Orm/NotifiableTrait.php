<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/15/2018
 * Time: 6:45 PM
 */

namespace TempestTools\Raven\Laravel\Orm;


use Doctrine\ORM\Event\PreFlushEventArgs;
use LaravelDoctrine\ORM\Notifications\Notifiable;
use Doctrine\ORM\Mapping as ORM;
use TempestTools\Common\Contracts\ArrayHelperContract;
use TempestTools\Raven\Contracts\Orm\Helper\NotificationHelperContract;
use TempestTools\Raven\Orm\Helper\NotificationHelper;
use TempestTools\Scribe\Contracts\Orm\Helper\EntityArrayHelperContract;

/**
 * A trait to apply to an entity to use a configuration in Scribe to send notifications.
 * @package TempestTools\Raven\Laravel\Orm
 */
trait NotifiableTrait
{
    use Notifiable;

    /**
     * @var string
     */
    protected $mailTo;

    /**
     * @var string
     */
    protected $nexmoTo;

    /**
     * @var NotificationHelperContract
     */
    protected $notificationHelper;

    /**
     * @var array these are properties that will override settings stored on the notification for the current context and mode. This can be used to another class (such as a repo) populate that notification settings for the entity.
     */
    protected $ravenOverrides =[];

    /**
     * a pre flush function that sends out notifications. It adds the notifications to the shared array so they can be processed by listeners created on the middleware
     *
     * @ORM\PreFlush
     * @param PreFlushEventArgs $args
     */
    public function ravenPreFlush(PreFlushEventArgs $args):void {

        /** @noinspection PhpParamsInspection */
        $this->setNotificationHelper(new NotificationHelper($this));
        $this->getNotificationHelper()->registerForNotifications();

    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail():string
    {
        return $this->getMailTo();
    }

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo():string
    {
        return $this->getNexmoTo();
    }

    /**
     * Runs the notifications on the entity
     *
     * @throws \RuntimeException
     */
    public function runNotifications():void {
        $this->getNotificationHelper()->runNotifications();
    }

    abstract public function getConfigArrayHelper():?EntityArrayHelperContract;

    /**
     * @return null|ArrayHelperContract
     */
    abstract public function getArrayHelper():?ArrayHelperContract;

    /**
     * @return string
     */
    public function getMailTo(): string
    {
        return $this->mailTo;
    }

    /**
     * @param string $mailTo
     */
    public function setMailTo(string $mailTo): void
    {
        $this->mailTo = $mailTo;
    }

    /**
     * @return string
     */
    public function getNexmoTo(): string
    {
        return $this->nexmoTo;
    }

    /**
     * @param string $nexmoTo
     */
    public function setNexmoTo(string $nexmoTo): void
    {
        $this->nexmoTo = $nexmoTo;
    }

    /**
     * @return NotificationHelperContract
     */
    public function getNotificationHelper(): NotificationHelperContract
    {
        return $this->notificationHelper;
    }

    /**
     * @param NotificationHelperContract $notificationHelper
     */
    protected function setNotificationHelper(NotificationHelperContract $notificationHelper): void
    {
        $this->notificationHelper = $notificationHelper;
    }

    /**
     * @return array
     */
    public function getRavenOverrides(): array
    {
        return $this->ravenOverrides;
    }

    /**
     * @param array $ravenOverrides
     */
    public function setRavenOverrides(array $ravenOverrides): void
    {
        $this->ravenOverrides = $ravenOverrides;
    }

}