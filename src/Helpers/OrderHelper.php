<?php

namespace Nurdaulet\FluxOrders\Helpers;

class OrderHelper
{
    const STATUS_NEW = 0;
    const SERVICE_COMMISSION_PERCENTAGE = 15;
    const STATUS_ACCEPTED = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_HISTORY = 3;
    const STATUS_CANCELED = 4;
    const STATUS_FINISHED = 5;
    const STATUS_EXPIRED = 6;
    const CANCELLED_ORDER_COUNT_LIMIT = 5;


    const STATUSES = [
        self::STATUS_NEW       => 'Новый',
        self::STATUS_ACCEPTED  => 'Принят',
        self::STATUS_ACTIVE    => 'Активный',
        self::STATUS_HISTORY    => 'История',
//        self::STATUS_CLIENT_CANCELED => 'Отменен (клиент)',
        self::STATUS_CANCELED => 'Отменен',
        self::STATUS_FINISHED  => 'Завершен',
        self::STATUS_EXPIRED  => 'Срок истек',
    ];
    const LORD_NEW_ORDER = 0;
    const CLIENT_PROCESSING = 0;
    const LORD_ACCEPTED = 1;
    const CLIENT_ACCEPTED = 1;
    const LORD_ISSUED = 2;
    const CLIENT_RECEIVED = 2;
    const CLIENT_CANCELED = 3;
    const LORD_WANTS_BACK = 3;
    const LORD_GOT = 4;
    const LORD_CANCELED = 5;
    const CLIENT_RETURNED = 4;
    public static function calcCommissionService($amount)
    {
        return round($amount *  self::SERVICE_COMMISSION_PERCENTAGE / 100) ;

    }
}
