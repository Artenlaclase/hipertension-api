<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HydrationRecord;

class HydrationRecordPolicy
{
    public function view(User $user, HydrationRecord $record)
    {
        return $user->id === $record->user_id;
    }

    public function update(User $user, HydrationRecord $record)
    {
        return $user->id === $record->user_id;
    }

    public function delete(User $user, HydrationRecord $record)
    {
        return $user->id === $record->user_id;
    }
}
