<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/16/2018
 * Time: 4:39 PM
 */

namespace TempestTools\Raven\Orm\Helper;


use TempestTools\Raven\Contracts\Orm\NotifiableEntityContract;
use TempestTools\Raven\Laravel\Orm\Notification\GeneralNotificationAbstract;

class NotificationHelper
{
    /**
     * @var NotifiableEntityContract
     */
    protected $entity;

    public function __construct(NotifiableEntityContract $entity)
    {
        $this->setEntity($entity);
    }

    /**
     * Runs the notifications on the entity
     *
     * @throws \RuntimeException
     */
    public function runNotifications():void {
        $entity = $this->getEntity();
        $config = $entity->getConfigArrayHelper()->getArray();
        if (isset($config['notifications'])) {
            $notificationsConfig = $config['notifications'];

            $params = ['self' => $this, 'entity'=>$entity, 'notificationsConfig'=>$notificationsConfig];
            /** @var array $notificationsConfig */
            foreach ($notificationsConfig as $key => $value) {
                if (isset($value['settings']['closure']) && $entity->getConfigArrayHelper()->parse($value['settings']['closure'], $params) === false) {
                    continue;
                }
                if ($value['notification'] !== null) {
                    $notification = $entity->getConfigArrayHelper()->parse($value['notification'], $params);
                    $this->populateNotificationDetails($notificationsConfig, $params, $notification);
                    $entity->notify($notification);
                }
            }
        }
    }

    /**
     * Populates all the details on the notification
     *
     * @param array $notificationsConfig
     * @param array $params
     * @param GeneralNotificationAbstract $notification
     * TODO: Use contract instead of RavenGeneralNotification
     * @throws \RuntimeException
     */
    protected function populateNotificationDetails(array $notificationsConfig, array $params, GeneralNotificationAbstract $notification):void {
        $entity = $this->getEntity();
        $via = [];
        $viaConfig = $notificationsConfig['via'];
        /** @var array $viaConfig */
        foreach ($viaConfig as $key => $value) {
            if (!isset($value['settings']['closure']) || $entity->getConfigArrayHelper()->parse($value['settings']['closure'], $params) === true) {
                array_walk(
                    $value,
                    function (&$item, $itemKey) use ($params, $entity) {
                        $newParams = $params;
                        $newParams['key'] = $itemKey;
                        $item = $entity->getConfigArrayHelper()->parse($item, $newParams);
                    });
                unset($value['settings']);
                $via[$key] = $value;
            }
        }
        $notification->setVia($via);
    }

    /**
     * @return NotifiableEntityContract
     */
    public function getEntity(): NotifiableEntityContract
    {
        return $this->entity;
    }

    /**
     * @param NotifiableEntityContract $entity
     */
    public function setEntity(NotifiableEntityContract $entity): void
    {
        $this->entity = $entity;
    }
}