<?php

namespace Nurdaulet\FluxOrders\Services;

use Illuminate\Support\Carbon;
use Nurdaulet\FluxOrders\Filters\OrderFilter;
use Nurdaulet\FluxOrders\Helpers\OrderHelper;
use Nurdaulet\FluxOrders\Helpers\PaymentHelper;
use App\Helpers\TransactionHelper;
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

class OrderLordFacadeService
{
    public function __construct()
    {
    }

    public function getMonthlyOrders($userId)
    {
        return config('flux-orders.models.order')::applyFilters(new OrderFilter(), ['lord_id' => $userId, 'this_month' => true])
            ->count();
    }


}
