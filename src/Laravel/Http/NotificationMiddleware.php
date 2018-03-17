<?php

namespace TempestTools\Common\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TempestTools\Common\Contracts\ArrayHelperContract;
use Illuminate\Events\Dispatcher;
use TempestTools\Raven\Contracts\Orm\NotifiableEntityContract;
use TempestTools\Raven\Laravel\Constants\ArrayHelperConstants;
use TempestTools\Scribe\Contracts\Events\SimpleEventContract;
use TempestTools\Scribe\Laravel\Events\Controller\PostDestroy;
use TempestTools\Scribe\Laravel\Events\Controller\PostStore;
use Illuminate\Support\Facades\Event;
use TempestTools\Scribe\Laravel\Events\Controller\PostUpdate;

/**
 * LocalizationMiddleware sets the locale with respect to the user's locale
 *
 * @link    https://github.com/tempestwf
 * @author  Jerome Erazo <https://github.com/jerazo>
 */
class NotificationMiddleware
{
    /**
     * @var ArrayHelperContract
     */
    protected $sharedArray;

    /**
     * Handle an incoming request. Stores the array helper for latter, and registers it's self to the scribe events that will trigger sending notifications.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $arrayHelper = $request->route()->getController()->getArrayHelper();
        $this->setSharedArray($arrayHelper);
        Event::subscribe($this);
        return $next($request);
    }

    /**
     * Fires notifications stored on the shared array
     *
     * @param SimpleEventContract $event
     * @throws \RuntimeException
     */
    public function fireNotification(SimpleEventContract $event):void {
        $array = $this->getSharedArray()->getArray();
        $registeredEntities = $array[ArrayHelperConstants::RAVEN_ARRAY_KEY] ?? [];
        /**
         * @var array $registeredEntities
         */
        /**
         * @var NotifiableEntityContract $entity
         */
        foreach ($registeredEntities as $entity) {
            $entity->runNotifications();
        }
    }
    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher |\Illuminate\Events\Dispatcher $events
     */
    public function subscribe(Dispatcher $events):void
    {
        $events->listen(
            PostStore::class,
            self::class . '@fireNotification'
        );
        $events->listen(
            PostUpdate::class,
            self::class . '@fireNotification'
        );
        $events->listen(
            PostDestroy::class,
            self::class . '@fireNotification'
        );

    }

    /**
     * @return ArrayHelperContract
     */
    public function getSharedArray(): ArrayHelperContract
    {
        return $this->sharedArray;
    }

    /**
     * @param ArrayHelperContract $sharedArray
     */
    public function setSharedArray(ArrayHelperContract $sharedArray): void
    {
        $this->sharedArray = $sharedArray;
    }
}