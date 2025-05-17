<?php

namespace App\Modules\User\Domain\Observers;

use App\Modules\User\Domain\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        $user->company_id = app('company')->company()->id;
    }
}
