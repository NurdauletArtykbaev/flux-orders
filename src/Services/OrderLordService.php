<?php

namespace Nurdaulet\FluxOrders\Services;

use Illuminate\Support\Str;
use Nurdaulet\FluxOrders\Helpers\OrderHelper;
use Nurdaulet\FluxOrders\Helpers\PaymentHelper;
use Nurdaulet\FluxOrders\Models\Order;
use App\Models\ReturnItemToLord;
use App\Models\User;
use App\Models\VerifyIssuedOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Nurdaulet\FluxItems\Repositories\ItemRepository;
use Nurdaulet\FluxOrders\Repositories\CanceledOrderRepository;
use Nurdaulet\FluxOrders\Repositories\OrderRepository;
use Nurdaulet\FluxOrders\Repositories\VerifyOrderRepository;

class OrderLordService
{
    public function __construct(private OrderService            $orderService,
                                private OrderRepository         $orderRepository,
                                private ItemRepository          $itemRepository,
                                private VerifyOrderRepository   $verifyOrderRepository,
                                private CanceledOrderRepository $canceledOrderRepository)
    {
    }

    public function getConfirmAcceptOrders($user)
    {
//        $lordId = $this->storeEmployeeService->getLordId($user);
        $lordId = $user->id;
        // userId lordId get from storePackage or User librarypackage
        return $this->orderRepository->paginate(
            ['lord_id' => $lordId, 'latest_by' => 'id'],
            ['item' => ['images'], 'city', 'rentType']
//            ['item' => ['images'], 'user', 'city', 'receiveMethod', 'paymentMethod']
        );
    }

    public function find($id) {
        return $this->orderRepository->find($id, [
            'item.images', 'item', 'user', 'rentType',
            'receiveMethod', 'paymentMethod', 'city'
        ]);
    }

    public function updateAcceptOrder($user, $id)
    {
        // librarpackage store or user
//        $lord = $this->storeEmployeeService->getLord($user);
        $lordId = $user->id;
        $order = $this->orderRepository->find($id, [], ['status' => OrderHelper::STATUS_NEW, 'lord_id' => $lordId]);
//         librarypackage
//        if (!app()->isProduction()) {
//            $this->payOrder($order, $lord);
//        }
        $this->orderRepository->update(
            [
                'id' => $id,
                'lord_id' => $lordId,
                'status' => OrderHelper::STATUS_NEW,
            ],
            [
                'status' => OrderHelper::STATUS_ACCEPTED,
                'accepted_at' => now(),
                'lord_status' => Order::LORD_ACCEPTED,
                'client_status' => Order::CLIENT_ACCEPTED,
            ]
        );
    }


    public function cancelOrder($user, $id, $cancelId = null)
    {
//        $lordId = $this->storeEmployeeService->getLordId($user);
        $lordId = $user->id;
//        $order = Order::whereHas('ad', fn($query) => $query->where('user_id', $lordId))
//            ->findOrFail($id);
        $order = $this->orderRepository->find($id, [], ['lord_id' => $lordId]);

        $this->orderRepository->update(
            [
                'id' => $id,
                'lord_id' => $lordId,
            ],
            [
                'lord_status' => Order::LORD_CANCELED,
                'status' => OrderHelper::STATUS_CANCELED,

            ]);
        $this->canceledOrderRepository->create($id, [
            'cancel_id' => $cancelId,
            'lord_id' => $lordId
        ]);
    }

    public function issueOrderSendCode($user, $id)
    {

        if (config('flux-orders.models.rent_order_accept_confirmation')) {
//            $lordId = $this->storeEmployeeService->getLordId($user);
            $lordId = $user->id;
            $order = $this->orderRepository->find($id, ['user'], ['lord_id' => $lordId]);
//            $order = Order::with('user')->findOrFail($id);
            //TODO send sms and save it to db
            $code = rand(10000, 99999);
            if (!app()->isProduction()) {
                $code = Str::substr($phone, -5);
            }
            $this->sendSmsToUser($order->user->phone, $code, 'issue');
            $this->verifyOrderRepository->updateIssueOrder(['order_id' => $order], [
                'code' => $code,
                'lord_id' => $lordId,
                'status' => 1
            ]);

        }

    }

    public function verifyIssueCode($id, $user, $code)
    {
        $lordId = $user->id;
        $order = $this->orderRepository->find($id, ['rentType'], ['lord_id' => $lordId]);
        if (config('flux-orders.models.rent_order_accept_confirmation')) {
            $codeFromDb = $this->verifyOrderRepository->firstIssueOrderCode($id);
            if ($code != $codeFromDb) {
                throw ValidationException::withMessages(['code' => 'code_incorrect']);
            }

            $this->orderRepository->update(
                [
                    'id' => $id
                ],
                [
                    'status' => Order::GENERAL_ACTIVE,
                    'lord_status' => Order::LORD_ISSUED,
                    'client_status' => Order::CLIENT_RECEIVED,
                    'date_from' => now(),
                    'date_to' => $this->orderService->calculateDateTo($order),
                ]);

            $this->verifyOrderRepository->updateIssueOrder(
                [
                    'order_id' => $order, 'code' => $code
                ],
                [
                    'status' => 2
                ]);
        } else {
            $this->orderRepository->update(
                [
                    'id' => $id
                ],
                [
                    'status' => Order::GENERAL_ACTIVE,
                    'lord_status' => Order::LORD_ISSUED,
                    'client_status' => Order::CLIENT_RECEIVED,
                    'date_from' => now(),
                    'date_to' => $this->orderService->calculateDateTo($order),
                ]);
        }
    }

    public function returnOrderSendCode($user, $id): void
    {

        $lordId = $user->id;
//            $lordId = $this->storeEmployeeService->getLordId($user);
        $order = $this->orderRepository->find($id, ['rentType'], ['lord_id' => $lordId]);

        if (config('flux-orders.models.rent_order_accept_confirmation')) {
            $phone = User::where('id', $order->user_id)->firstOrFail()->phone;
            $code = rand(10000, 99999);
            if (!app()->isProduction()) {
                $code = Str::substr($phone, -5);
            }
            $this->sendSmsToUser($phone, $code, 'return');
            $this->verifyOrderRepository->updateReturnOrder(
                ['order_id' => $order->id],
                [
                    'code' => $code,
                    'user_id' => $lordId,
                    'status' => 1
                ],
            );
        }
    }

    public function verifyReturnCode($user, $code, $id): void
    {
        $lordId = $user->id;
//            $lordId = $this->storeEmployeeService->getLordId($user);
        $order = $this->orderRepository->find($id, [], ['lord_id' => $lordId]);

        if (config('flux-orders.models.rent_order_accept_confirmation')) {
            $codeFromDb = $this->verifyOrderRepository->firstReturnOrderCode($id);
            if ($code != $codeFromDb) {
                throw ValidationException::withMessages(['code' => 'code_incorrect']);
            }
            $this->orderRepository->update(
                [
                    'id' => $id
                ],
                [
                    'status' => OrderHelper::STATUS_FINISHED,
                    'lord_status' => Order::LORD_GOT,
                    'client_status' => Order::CLIENT_RETURNED,
                ]);

            $this->verifyOrderRepository->updateReturnOrder(
                [
                    'order_id' => $order->id,
                    'code' => $code
                ],
                [
                    'status' => 2
                ],
            );
        } else {
            $this->orderRepository->update(
                [
                    'id' => $id
                ],
                [
                    'status' => OrderHelper::STATUS_FINISHED,
                    'lord_status' => Order::LORD_GOT,
                    'client_status' => Order::CLIENT_RETURNED,
                ]);
        }
    }

    private function sendSmsToUser($phone, $code, $context)
    {
        if (!app()->isProduction()) {
            return;
        }
        $text = match ($context) {
            'issue' => "Код для аренды товара ",
            'return' => "Код для возвращение товара ",
        };

        /* librarypackage smsk*/
        \SmsKz::to($phone)->text($text . evn('APP_NAME') . ": $code")->send();
    }

    private function payOrder($order, $lord)
    {
        $amount = OrderHelper::calcCommissionService($order->total_price);
//        $isPaid = $lord->transactions()->where('fields_json->order_id', $order->id)
//            ->where('status', PaymentHelper::STATUS_PAID)->exists();
//        if ($isPaid) {
//            return null;
//        }
//        librarypackage wallet
//        $lord->loadMissing('balance');
//
//        throw_if($lord->balance->money < 0, abort(400, 'Недостаточна средств.Пополните баланс'));
//        $lord->balance->money = $lord->balance->money - $amount;
//        DB::beginTransaction();
//        $lord->balance->save();
//        $data = [
//            'amount' => $amount,
//            'fields_json' => ['order_id' => $order->id],
//            'type' => TransactionHelper::TYPE_ORDER,
//            'status' => PaymentHelper::STATUS_PAID
//        ];
//        $lord->transactions()->create($data);
        DB::commit();
    }
}
