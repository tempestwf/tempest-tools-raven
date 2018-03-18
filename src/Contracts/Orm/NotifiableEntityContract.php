<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/16/2018
 * Time: 4:45 PM
 */

namespace TempestTools\Raven\Contracts\Orm;


use Doctrine\ORM\Event\PreFlushEventArgs;
use TempestTools\Common\Contracts\ArrayHelperContract;
use TempestTools\Scribe\Contracts\Orm\Helper\EntityArrayHelperContract;

interface NotifiableEntityContract
{
    /**
     * a pre flush function that sends out notifications. It adds the notifications to the shared array so they can be processed by listeners created on the middleware
     *
     * @ORM\PreFlush
     * @param PreFlushEventArgs $args
     */
    public function ravenPreFlush(PreFlushEventArgs $args):void;
    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail():string;

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo():string;

    /**
     * Runs the notifications on the entity
     *
     * @throws \RuntimeException
     */
    public function runNotifications():void;

    public function getConfigArrayHelper():?EntityArrayHelperContract;

    /**
     * @return null|ArrayHelperContract
     */
    public function getArrayHelper():?ArrayHelperContract;

    /**
     * @return string
     */
    public function getMailTo(): string;

    /**
     * @param string $mailTo
     */
    public function setMailTo(string $mailTo): void;
    /**
     * @return string
     */
    public function getNexmoTo(): string;
    /**
     * @param string $nexmoTo
     */
    public function setNexmoTo(string $nexmoTo): void;

    /**
     * Send the given notification.
     *
     * @param  mixed  $instance
     * @return void
     */
    public function notify($instance);

    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function routeNotificationFor($driver);

    /**
     * @return array
     */
    public function getRavenOverrides(): array;

    /**
     * @param array $ravenOverrides
     */
    public function setRavenOverrides(array $ravenOverrides): void;
}