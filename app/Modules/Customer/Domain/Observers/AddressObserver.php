<?php

namespace App\Modules\Customer\Domain\Observers;

use App\Modules\Customer\Domain\Models\Address;

class AddressObserver
{
    public function creating(Address $address)
    {
        if (!request()->customer_id) {
            return;
        }
        
        $address->customer_id = request()->customer_id;
    }
}
