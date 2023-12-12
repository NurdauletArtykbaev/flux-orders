<?php


return [
    'models' => [
        'order' => \Nurdaulet\FluxOrders\Models\Order::class,
        'canceled_order' => \Nurdaulet\FluxOrders\Models\CanceledOrder::class,
        'cancel_reason' => \Nurdaulet\FluxOrders\Models\CancelReason::class,
        'payment_method' => \Nurdaulet\FluxOrders\Models\PaymentMethod::class,
        'user' => \Nurdaulet\FluxOrders\Models\User::class,
        'verify_issued_order' => \Nurdaulet\FluxOrders\Models\VerifyIssuedOrder::class,
        'verify_return_order' => \Nurdaulet\FluxOrders\Models\VerifyReturnOrder::class,

    ],
    'languages' => [
        'ru', 'en', 'kk'
    ],
    'options' => [
        'rent_order_accept_confirmation' => false,// выделить на env file
        'rent_order_return_confirmation' => false,// выделить на env file
        'use_filament_admin_panel' => true,
        'send_sms_when_created_order' => false,
        'send_telegram_when_created_order' => false,
        'use_list_items_count' => false,
        'is_enabled_cart' => true,
        'cache_expiration' => 269746,
        'use_roles' => false
    ],
];
