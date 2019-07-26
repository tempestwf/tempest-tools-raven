<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/17/2018
 * Time: 3:24 PM
 */

namespace TempestTools\Raven\Contracts\Orm\Helper;

use TempestTools\Raven\Contracts\Orm\NotifiableEntityContract;

interface NotificationHelperContract
{
    public function registerForNotifications(): void;

    /**
     * Runs the notifications on the entity
     *
     * @throws \RuntimeException
     */
    public function runNotifications(): void;

    /**
     * @return NotifiableEntityContract
     */
    public function getEntity(): NotifiableEntityContract;

    /**
     * @param NotifiableEntityContract $entity
     */
    public function setEntity(NotifiableEntityContract $entity): void;
}