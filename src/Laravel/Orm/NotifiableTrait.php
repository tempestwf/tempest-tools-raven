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
use TempestTools\Scribe\Contracts\Orm\Helper\EntityArrayHelperContract;

trait NotifiableTrait
{
    use Notifiable;
    /**
     * a pre flush function that sends out notifications
     * @ORM\PreFlush
     */
    public function ravenPreFlush():void {
        //$config = $this->getConfigArrayHelper()->getArray();
        //if (isset($config['notifications'])) {
            //$notificationsConfig =
        //}

        //$this->notify();
    }

    abstract public function getConfigArrayHelper():?EntityArrayHelperContract;

}