<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/15/2018
 * Time: 6:45 PM
 */

namespace TempestTools\Raven\Laravel\Orm;


use LaravelDoctrine\ORM\Notifications\Notifiable;
use Doctrine\ORM\Mapping as ORM;
use TempestTools\Raven\Orm\Helper\NotificationHelper;
use TempestTools\Scribe\Contracts\Orm\Helper\EntityArrayHelperContract;

trait NotifiableTrait
{
    use Notifiable;
    /**
     * a pre flush function that sends out notifications
     * @ORM\PreFlush
     */
    public function ravenPreFlush():void {

    }

    /**
     * Runs the notifications on the entity
     */
    public function runNotifications():void {
        /** @noinspection PhpParamsInspection */
        $helper = new NotificationHelper($this);
        $helper->runNotifications();
    }

    abstract public function getConfigArrayHelper():?EntityArrayHelperContract;

}