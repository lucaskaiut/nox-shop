<?php

namespace App\Modules\Customer\Domain\Observers;

use App\Modules\Customer\Domain\Models\Customer;

class CustomerObserver
{
    public function creating(Customer $customer)
    {
        $customer->company_id = app('company')->company()->id;
    }
}
