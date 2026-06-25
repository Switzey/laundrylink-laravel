<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * @param  array<string, mixed>|null  $data
     */
    public function create(User $user, string $title, string $message, ?string $type = 'general', ?array $data = null): Notification
    {
        return Notification::query()->create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public function createForAdmins(string $title, string $message, ?string $type = 'general', ?array $data = null): Collection
    {
        return User::query()
            ->where('role', 'admin')
            ->get()
            ->map(fn (User $admin) => $this->create($admin, $title, $message, $type, $data));
    }
}
