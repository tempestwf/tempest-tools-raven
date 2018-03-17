<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/16/2018
 * Time: 4:45 PM
 */

namespace TempestTools\Raven\Contracts\Orm;


use TempestTools\Scribe\Contracts\Orm\Helper\EntityArrayHelperContract;

interface NotifiableEntityContract
{
    public function notify($instance);
    public function getConfigArrayHelper():?EntityArrayHelperContract;
}