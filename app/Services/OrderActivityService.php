<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderActivity;
use App\Models\User;

class OrderActivityService
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function log(Order $order, string $action, string $description, ?User $user = null, ?array $metadata = null): OrderActivity
    {
        return OrderActivity::query()->create([
            'order_id' => $order->id,
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
