<?php

namespace TempestTools\Raven\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TempestTools\Common\Constants\CommonArrayObjectKeyConstants;
use Illuminate\Events\Dispatcher;
use TempestTools\Common\Contracts\HasArrayHelperContract;
use TempestTools\Common\Exceptions\Laravel\Http\Middleware\CommonMiddlewareException;
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
     * Handle an incoming request. Stores the array helper for latter, and registers it's self to the scribe events that will trigger sending notifications.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \TempestTools\Common\Exceptions\Laravel\Http\Middleware\CommonMiddlewareException
     */
    public function handle(Request $request, Closure $next)
    {
        $controller = $request->route()->getController();
        if ($controller instanceof HasArrayHelperContract === false) {
            throw CommonMiddlewareException::controllerDoesNotImplement('HasArrayHelperContract');
        }
        //$arrayHelper = $controller->getArrayHelper();
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
        $array = $event->getEventArgs()['controller']->getArrayHelper()->getArray();

        $registeredEntities = $array[CommonArrayObjectKeyConstants::ORM_KEY_NAME][ArrayHelperConstants::RAVEN_ARRAY_KEY] ?? [];
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
}