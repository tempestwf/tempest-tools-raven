<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/16/2018
 * Time: 4:39 PM
 */

namespace TempestTools\Raven\Orm\Helper;


use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;
use TempestTools\Raven\Contracts\Orm\Helper\NotificationHelperContract;
use TempestTools\Raven\Contracts\Orm\NotifiableEntityContract;
use TempestTools\Raven\Constants\ArrayHelperConstants;
use TempestTools\Raven\Constants\ViaTypesConstants;
use TempestTools\Raven\Laravel\Notifications\GeneralNotificationAbstract;

class NotificationHelper implements NotificationHelperContract
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
     * registers the notification in a shared array for access by the middleware
     *
     * @throws \RuntimeException
     */
    public function registerForNotifications():void {
        $entity = $this->getEntity();
        if ($entity->getArrayHelper() !== null) {
            $config = $entity->getConfigArrayHelper()->getArray();
            $notificationsConfig = $config['notifications'] ?? [];
            $params = $params = ['self' => $this, 'entity'=>$entity, 'notificationsConfig'=>$notificationsConfig];
            $enabled = $config['notifications']['enable'] ?? true;
            $enabled = $this->getEntity()->getConfigArrayHelper()->parse($enabled, $params);
            if ($enabled === true && isset($config['notifications']) === true) {
                if (isset($entity->getArrayHelper()->getArray()[CommonArrayObjectKeyConstants::ORM_KEY_NAME][ArrayHelperConstants::RAVEN_ARRAY_KEY])) {
                    $entity->getArrayHelper()->getArray()[CommonArrayObjectKeyConstants::ORM_KEY_NAME][ArrayHelperConstants::RAVEN_ARRAY_KEY] = [];
                }
                $entity->getArrayHelper()->getArray()[CommonArrayObjectKeyConstants::ORM_KEY_NAME][ArrayHelperConstants::RAVEN_ARRAY_KEY][] = $this->getEntity();
            }
        }
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
            $notificationsConfig = array_replace_recursive($notificationsConfig, $entity->getRavenOverrides());

            $params = ['self' => $this, 'entity'=>$entity, 'notificationsConfig'=>$notificationsConfig];
            /** @var array $notificationsConfig */
            foreach ($notificationsConfig as $key => $value) {
                $enabled = $value['enable'] ?? true;
                $enabled = $entity->getConfigArrayHelper()->parse($enabled, $params);
                if ($enabled === false || (isset($value['settings']['closure']) && $entity->getConfigArrayHelper()->parse($value['settings']['closure'], $params) === false)) {
                    continue;
                }
                if ($value['notification'] !== null) {
                    $notification = $entity->getConfigArrayHelper()->parse($value['notification'], $params);
                    $this->populateNotificationDetails($value, $params, $notification);
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
                if (isset($value['to'])) {
                    $value['to'] = $entity->getConfigArrayHelper()->parse($value['to'], $params);
                    if ($key === ViaTypesConstants::MAIL) {
                        $entity->setMailTo($value['to']);
                    } else if ($key === ViaTypesConstants::NEXMO) {
                        $entity->setNexmoTo($value['to']);
                    }
                }
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