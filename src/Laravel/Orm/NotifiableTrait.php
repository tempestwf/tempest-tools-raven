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
use TempestTools\Raven\Laravel\Constants\ArrayHelperConstants;
use TempestTools\Raven\Orm\Helper\NotificationHelper;
use TempestTools\Scribe\Contracts\Orm\Helper\EntityArrayHelperContract;

/**
 * A trait to apply to an entity to use a configuration in Scribe to send notifications.
 * TODO: turn into a better contract
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
     * a pre flush function that sends out notifications. It adds the notifications to the shared array so they can be processed by listeners created on the middleware
     *
     * @ORM\PreFlush
     * @param PreFlushEventArgs $args
     */
    public function ravenPreFlush(PreFlushEventArgs $args):void {
        $array = $this->getArrayHelper()->getArray();
        if (isset($array[ArrayHelperConstants::RAVEN_ARRAY_KEY])) {
            $array[ArrayHelperConstants::RAVEN_ARRAY_KEY] = [];
        }
        $array[ArrayHelperConstants::RAVEN_ARRAY_KEY][] = $this;
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
        /** @noinspection PhpParamsInspection */
        $helper = new NotificationHelper($this);
        $helper->runNotifications();
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

}