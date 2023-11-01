<?php

namespace App\Observers;

use App\Helpers\NotificationHelper;
use App\Helpers\OrderHelper;
use App\Jobs\Notification\NotifyAboutOrderJob;
use App\Models\Order;
use App\Models\SentPushNotification;
use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\DB;

class OrderObserver
{

    public function created(Order $order)
    {
        $order->loadMissing('ad.images');
        $data = [
            'ad' => [
                'id' => $order->ad?->id,
                'name' => $order->ad?->name,
                'image' => $order->ad->images->first()?->fullUrl,
                'last_image' => $order->ad->images->last()?->fullUrl,
            ]
        ];
        $data['order_id'] = $order->id;
        $data['lord_id'] = $order->ad->user_id;
        NotifyAboutOrderJob::dispatch($order, NotificationHelper::LORD_ORDER_NEW, $data, [], $order->ad->user_id)->onQueue('notification');
    }

    public function updated(Order $order)
    {
        if ($order->status != $order->getOriginal('status')) {
            $key = "";
            $keyLord = "";
            $replace = [];

            if ($order->status == OrderHelper::STATUS_ACCEPTED) {
                $key = NotificationHelper::CLIENT_ORDER_ACCEPT;
                $keyLord = NotificationHelper::LORD_ORDER_ACCEPT;
            } elseif ($order->status == OrderHelper::STATUS_ACTIVE) {
                $key = NotificationHelper::CLIENT_ORDER_ISSUE;
                $keyLord = NotificationHelper::LORD_ORDER_ISSUE;
            } elseif ($order->status == OrderHelper::STATUS_CANCELED) {
                $key = NotificationHelper::CLIENT_ORDER_CANCEL;
                $keyLord = NotificationHelper::LORD_ORDER_CANCEL;
                $replace = [
                    'order_number' => $order->id
                ];
                if (app()->isProduction()) {
                    $order->notify(new TelegramNotification());
                }

            } elseif ($order->status == OrderHelper::STATUS_FINISHED) {
                $key = NotificationHelper::CLIENT_ORDER_RECEIVE;
                $keyLord = NotificationHelper::LORD_ORDER_GOT;
                $replace = [
                    'order_number' => $order->id
                ];
            }
            $order->loadMissing(['ad.images']);
            if ($order->ad) {
                $data = [
                    'ad' => [
                        'id' => $order->ad?->id,
                        'name' => $order->ad?->name,
                        'image' => $order->ad->images->first()?->fullUrl,
                        'last_image' => $order->ad->images->last()?->fullUrl,
                    ]
                ];

                $data['order_id'] = $order->id;
                $data['user_id'] = $order->user_id;
                NotifyAboutOrderJob::dispatch($order, $key, $data, $replace)->onQueue('notification');
                if (!empty($keyLord)) {

                    $data['order_id'] = $order->id;
                    $data['lord_id'] = $order->ad->user_id;
                    unset($data['user_id']);
                    NotifyAboutOrderJob::dispatch($order, $keyLord, $data, $replace, $order->ad->user_id)->onQueue('notification');
                }
            }



        }
    }

    public function deleting(Order $order)
    {
//        $order->receiveMethod()->delete();
////        $order->categories()->sync([]);
//        $order->receiveMethod()->sync([]);
//        $order->returnMethods()->sync([]);
//        $order->protectMethods()->sync([]);
//        $order->orders()->delete();
//        $order->viewHistory()->delete();
    }
}
