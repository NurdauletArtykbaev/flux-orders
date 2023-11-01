<?php

namespace Nurdaulet\FluxOrders\Services;

use Nurdaulet\FluxOrders\Helpers\OrderHelper;
use App\Helpers\RentalDayHelper;
use App\Helpers\TransactionHelper;
use App\Helpers\UserHelper;
use App\Models\CanceledOrder;
use App\Models\Order;
use App\Notifications\TelegramNotification;
use Nurdaulet\FluxItems\Repositories\ItemRepository;
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
            'item.images', 'item', 'rentType',
            'receiveMethod', 'paymentMethod', 'city'
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


//    public function downloadOrderContract($id): JsonResponse
//    {
//        $contractUrl = $this->getSellerContract($id);
//
//        return response()
//            ->json(['file_url' => $contractUrl]);
//    }

//    public function getSellerContract($id): string
//    {
//        $order = $this->orderRepository->find($id, ['user', 'ad.user']);
//
//        $replacement = [
//            '(Ф.И.О.) арендатора' => Str::ucfirst($order->ad->user->surname) . ' ' . Str::ucfirst($order->ad->user->name),
//            '(Ф.И.О.) арендадателя' => Str::ucfirst($order->user->surname) . ' ' . Str::ucfirst($order->user->name),
//            '(Ф.И.О.)' => Str::ucfirst($order->user->surname) . ' ' . Str::ucfirst($order->user->name),
//            '(ИИН)' => $order->user->iin,
//            '(товар)' => $order->ad->name,
//        ];
//
//        if (empty($order->ad->user->contract)) {
//            abort(400, trans('Contract not found'));
//        }
//
//        $url = config('filesystems.disks.s3.url') . '/' . $order->ad->user->contract;
//
//        $contractExtension = pathinfo($url, PATHINFO_EXTENSION);
//
//        try {
//            if ($contractExtension == 'pdf') {
//                $contract = $this->identificationService->handleContractPdf($url, $replacement);
//                $path = UserHelper::TEMPT_CONTRACT_DIR . Str::uuid() . '_contract' . '.pdf';
//            } else {
//                $contract = $this->identificationService->handleContractDoc($url, $replacement);
//                $path = UserHelper::TEMPT_CONTRACT_DIR . Str::uuid() . '_contract' . '.docx';
//            }
//
//            Storage::disk('s3')->put($path, $contract);
//
//            return Storage::disk('s3')->url($path);
//        } catch (\Exception $exception) {
//            abort(400, 'Попробуйте попозже');
//        }
//    }

    public function calculateOrderTotalPriceIfUsedBonus($isUsedBonus, $user, $totalPrice)
    {
        if ($isUsedBonus == 'true' ) {
            // librarypackage wallet
//            $user->loadMissing('balance');

//        if ($isUsedBonus == 'true' && $user->balance && $user->balance->bonus > 0) {
//            if ($user->balance->bonus) {
//                $usedBonus = $user->balance->bonus;
//                if ($totalPrice >= $user->balance->bonus) {
//                    $totalPrice = $totalPrice - $user->balance->bonus;
//                } else {
//                    $bonus = $user->balance->bonus - $totalPrice;
//                    $usedBonus = $user->balance->bonus - $bonus;
//                    $totalPrice = 0;
//                }
//                return [$totalPrice, $usedBonus];
//            }
        }
        return [$totalPrice, 0];
    }

    public function create($user, $request)
    {
        $this->checkByLimitCancelledOrder($user);
        $item = $this->itemRepository->find($request->get('item_id'), ['user']);
        $isUsedBonus = $request->input('is_used_bonus', false);

        $totalPrice = $request->get('total_price');
        [$totalPrice, $usedBonus] = $this->calculateOrderTotalPriceIfUsedBonus($isUsedBonus, $user, $totalPrice);

        $data = $this->prepareCreateData($user,$request->all());

        DB::beginTransaction();
        $order = $this->orderRepository->create($data);
        $this->payOrderIfUsedBonus($isUsedBonus, $user, $order, $request->get('total_price'));
        DB::commit();

        if (app()->isProduction()) {
            if (config('flux-orders.options.send_sms_when_created_order', false)) {
                if ($item?->user?->phone) {
                    \SmsKz::to($item->user?->phone)->text("Поступил Новый заказ  " . env('APP_NAME') . ". Успейте обработать заказ!")
                        ->send();
                }
            }
            if (config('flux-orders.options.send_telegram_when_created_order', false)) {
//                // telgoram notification text
//                $order->notify(new TelegramNotification());
            }
        }
        return $order;
    }

    public function payOrderIfUsedBonus($isUsedBonus, $user, $order, $totalPrice): void
    {
        if ($isUsedBonus == 'true' ) {
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

    private function prepareCreateData($user, $data)
    {
        $item = $this->itemRepository->find($data['item_id'], ['user']);
        $isUsedBonus = $data['is_used_bonus'] ?? false;
        $totalPrice = $data['total_price'];

        [$totalPrice, $usedBonus] = $this->calculateOrderTotalPriceIfUsedBonus($isUsedBonus, $user, $totalPrice);

        return [
            'item_id' => $data['item_id'],
            'city_id' => (int)$data['city_id'],
            'phone' => $data['phone'] ?? null,
            'user_id' => $user->id,
            'lord_id' => $item->user_id,
            'receive_method_id' => $data['receive_method_id'] ?? null,
            // receive_method_id - null
            'delivery_price' => $data['delivery_price'] ?? null,
            // delivery_price - 0
            'delivery_date' => $data['delivery_date'] ?? null,
            // delivery_date - null
            'payment_method_id' => $data['payment_method_id'] ?? null,
            'platform' => request()->header('platform'),
            'comment' => $data['comment'] ?? null,
            'rent_price' => $data['rent_price'] ?? null,
            'total_price' => $totalPrice,
            'address' => $data['address'] ?? null,
            'lat' => $data['lat'] ?? null,
            'lng' => $data['lng'] ?? null,
            'rent_value' => $data['rent_value'] ?? null,
            'delivery_time' => $data['delivery_time'] ?? null,
            'is_fast_delivery' => ($data['is_fast_delivery'] ?? null) == 'true',
            'rent_type_id' => $data['rent_type_id'] ?? null,
            'status' => Order::GENERAL_NEW,
            'is_used_bonus' => $isUsedBonus,
            'bonus' => $usedBonus,
            'client_status' => OrderHelper::CLIENT_PROCESSING,
            'lord_status' => OrderHelper::LORD_NEW_ORDER,
        ];

    }

    public function checkByLimitCancelledOrder($user)
    {
        $cancelledOrdersCount = $this->orderRepository->count([
            'user_id' => $user->id,
            'created_at' => now(),
            'status' => OrderHelper::STATUS_CANCELED,
        ]);

        if ($cancelledOrdersCount >= OrderHelper::CANCELLED_ORDER_COUNT_LIMIT) {
            abort(400, trans('errors.orders.cancelled_limit'));
        }
    }

    public function calculateDateTo($order)
    {
        $dateFrom = now();
        return match ($order->rentType->slug) {
            RentalDayHelper::TYPE_HOUR => $dateFrom->copy()->addHours((int)$order->rent_value),
            RentalDayHelper::TYPE_DAY => $dateFrom->copy()->addDays((int)$order->rent_value),
            RentalDayHelper::TYPE_WEEK => $dateFrom->copy()->addWeeks((int)$order->rent_value),
            RentalDayHelper::TYPE_MONTH => $dateFrom->copy()->addMonths((int)$order->rent_value),
            default => $dateFrom->copy()->addWeek(),
        };
    }
//
//    public function getRentDayCountAndDate($order)
//    {
//        $startDate = Carbon::create($order->accepted_at ?: $order->created_at);
//        $countWithText = $order->rent_value . ' ';
//        $acceptedAt = Carbon::create($order->accepted_at ?? $order->created_at);
//        switch ($order->rental_day_type) {
//            case RentalDayHelper::TYPE_HOUR:
//                $days = round(((int)$order->rent_value / 24));
//                $countWithText .= 'час(-ов)';
//                $endDate = $acceptedAt->copy()->addDays($days);
//                break;
//            case RentalDayHelper::TYPE_DAY:
//                $countWithText .= 'дня(-ей)';
//                $endDate = $acceptedAt->copy()->addDays((int)$order->rent_value);
//                break;
//            case RentalDayHelper::TYPE_WEEK:
//                $countWithText .= 'неделю(-и)';
//                $endDate = $acceptedAt->copy()->addWeeks((int)$order->rent_value);
//                break;
//            case RentalDayHelper::TYPE_MONTH:
//                $countWithText .= 'месяц(-а,-ев)';
//                $endDate = $acceptedAt->copy()->addMonths((int)$order->rent_value);
//                break;
//            case RentalDayHelper::TYPE_FLIGHT:
//                $countWithText .= 'рейс(-а)';
//                $endDate = "«___» ____________ 202 __";
//                break;
//            case RentalDayHelper::TYPE_SHIFT:
//                $countWithText .= 'смену(-ы)';
//                $endDate = "«___» ____________ 202 __";
//                break;
//            default:
//                $countWithText .= 'дня(-ей)';
//                $endDate = "«___» ____________ 202 __";
//        }
//        $startDate = gettype($startDate) != 'string' ? $startDate->translatedFormat("«d» F Y") : $startDate;
//        $endDate = gettype($endDate) != 'string' ? $endDate->translatedFormat("«d» F Y") : $endDate;
//        return [$startDate, $endDate, $countWithText];
//    }

}
