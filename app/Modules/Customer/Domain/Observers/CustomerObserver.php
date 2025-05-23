<?php

namespace App\Modules\Customer\Domain\Observers;

use App\Modules\Customer\Domain\Models\Customer;
use App\Modules\User\Domain\Models\User;

class CustomerObserver
{
    public function creating(Customer $customer)
    {
        $customer->company_id = app('company')->company()->id;

        $user = User::create([
            'name' => $customer->first_name . ' ' . $customer->last_name,
            'email' => $customer->email,
            'password' => $customer->password,
            'type' => 'customer',
        ]);

        $customer->user_id = $user->id;
    }

    public function updating(Customer $customer)
    {
        $customer->user()->first()->update([
            'name' => $customer->first_name . ' ' . $customer->last_name,
            'email' => $customer->email,
        ]);
    }
}
