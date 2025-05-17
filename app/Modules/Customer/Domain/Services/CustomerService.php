<?php

namespace App\Modules\Customer\Domain\Services;

use App\Modules\Core\Domain\Traits\ServiceTrait;
use App\Modules\Customer\Domain\Models\Customer;

class CustomerService
{
    use ServiceTrait;

    public function model(): string 
    {
        return Customer::class;
    }
        
    public function hasManyRelations(): array
    {
        return [];
    }
}
