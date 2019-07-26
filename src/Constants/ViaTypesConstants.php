<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 3/15/2018
 * Time: 7:32 PM
 */

namespace TempestTools\Raven\Constants;


use LaravelDoctrine\ORM\Notifications\DoctrineChannel;

class ViaTypesConstants
{
    public const DOCTRINE = DoctrineChannel::class;
    public const MAIL = 'mail';
    public const DATABASE = 'database';
    public const BROADCAST = 'broadcast';
    public const NEXMO = 'nexmo';
    public const SLACK  = 'slack ';
}