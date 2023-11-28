<?php

namespace Nurdaulet\FluxOrders\Services;

use Nurdaulet\FluxItems\Facades\ItemsFacade;
use Nurdaulet\FluxItems\Helpers\ItemHelper;
use Nurdaulet\FluxOrders\Helpers\OrderHelper;
use Nurdaulet\FluxOrders\Models\Order;
use Nurdaulet\FluxItems\Repositories\ItemRepository;
use Nurdaulet\FluxOrders\Models\Cart;
use Nurdaulet\FluxOrders\Repositories\CanceledOrderRepository;
use Nurdaulet\FluxOrders\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private OrderRepository         $orderRepository,
        private ItemRepository          $itemRepository,
        private CanceledOrderRepository $canceledOrderRepository)
    {
    }

    public function getByUser($user, $filters = [])
    {

        $filters['user_id'] = $user->id;
        $filters['latest_by'] = 'id';
        return $this->orderRepository->paginate($filters);
    }

    public function show($id)
    {
        return $this->orderRepository->find($id, [
            'items.images',
//            'rentType',
            'address',
            'receiveMethod',
            'paymentMethod', 'city'
        ]);
//        $order->ad->user->contract_url = $order->ad->user->contract_url ?? null;
//        return $order;
    }

    public function cancelByUser($user, $id, $cancelId = null)
    {
        $this->orderRepository->update(['id' => $id, 'user_id' => $user->id], [
            'client_status' => OrderHelper::CLIENT_CANCELED,
            'status' => OrderHelper::STATUS_CANCELED,
        ]);

        $this->canceledOrderRepository->create($id, [
            'cancel_id' => $cancelId,
            'client_id' => $user->id
        ]);
    }

    public function createFromCart($user, $data)
    {
        $cart = Cart::with(['items' => fn($query) => $query->with(ItemHelper::getPriceRelation())])
            ->firstOrFail();

        $lords = config('flux-items.models.user')::whereIn('id', $cart->items->pluck('user_id')->toArray())->get();

        foreach ($lords as $lord) {
            $items = $cart->items->where('user_id', $user->id);
            $itemsTypes = $items->pluck('type')->unique()->toArray();
            foreach ($itemsTypes as $itemsType) {
                $orderData = $this->prepareCreateFromCartOrderData($cart, $user, $lord, $items);
                DB::beginTransaction();
                $order = Order::create($orderData);
                $orderItems = $this->prepareCreateFromCartOrderItemsData($items);
                $order->items()->attach($orderItems);
                DB::commit();
            }
        }
        $cart->delete();
//        $data = $this->prepareCreateData($user, $data);

//        $order = $this->orderRepository->create($data);
//        $this->payOrderIfUsedBonus($isUsedBonus, $user, $order, $totalPrice);


//        if (app()->isProduction()) {
//            if (config('flux-orders.options.send_sms_when_created_order', false)) {
////                if ($item?->user?->phone) {
////                    \SmsKz::to($item->user?->phone)->text("Поступил Новый заказ  " . env('APP_NAME') . ". Успейте обработать заказ!")
////                        ->send();
////                }
//            }
//            if (config('flux-orders.options.send_telegram_when_created_order', false)) {
////                // telgoram notification text
////                $order->notify(new TelegramNotification());
//            }
//        }
    }

    public function payOrderIfUsedBonus($isUsedBonus, $user, $order, $totalPrice): void
    {
        if ($isUsedBonus == 'true') {
//        if ($isUsedBonus == 'true' && $user->balance && $user->balance->bonus > 0) {
            // pay librarypackage  wallet
//            if ($user->balance->bonus) {
//            $user->loadMissing('balance');
//                $amount = $user->balance->bonus;
//                if ($totalPrice >= $user->balance->bonus) {
//                    $bonus = 0;
//                } else {
//                    $bonus = $user->balance->bonus - $totalPrice;
//                    $amount = $user->balance->bonus - $bonus;
//                }
//                $user->balance()->update([
//                    'bonus' => $bonus
//                ]);
//                $user->transactions()->create([
//                    'fields_json' => ['id' => $order->id],
//                    'type' => TransactionHelper::TYPE_ORDER,
//                    'bonus' => $user->balance->bonus,
//                    'money' => $user->balance->money,
//                    'status' => PaymentHelper::STATUS_PAID,
//                    'amount' => $amount,
//                ]);
//            }
        }
    }

    private function prepareCreateFromCartOrderItemsData($items)
    {

        $orderItems = [];
        foreach ($items as $item) {
            [$price, $oldPrice] = ItemHelper::getPrice($item, $item->pivot->rent_type_id);
            $orderItems[$item->id] = [
                'rent_type_id' => $item->pivot->rent_type_id,
                'quantity' => $item->pivot->quantity,
                'rent_value' => $item->pivot->rent_value,
                'price' => $price < $oldPrice ? $oldPrice : $price,
            ];
        }
        return $orderItems;
    }

    private function prepareCreateFromCartOrderData($cart, $user, $lord, $items)
    {
        return [
            'type' => OrderHelper::getTypeFromItemType($items->first()->type),
            'phone' => $cart->phone,
            'user_id' => $user->id,
            'full_name' => $cart->full_name,
            'city_id' => request()->header('city_id'),
            'lord_id' => $lord->id,
            'user_address_id' => $items->pluck('pivot.user_address_id')->unique()->first(),
            'payment_method_id' => $cart->payment_method_id,
            'receive_method_id' => $items->pluck('pivot.receive_method_id')->unique()->first(),
            'platform' => request('platform'),
            'status' => Order::GENERAL_NEW,
            'client_status' => OrderHelper::CLIENT_PROCESSING,
            'lord_status' => OrderHelper::LORD_NEW_ORDER,
        ];
    }
}
