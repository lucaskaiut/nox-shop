<?php

namespace App\Modules\Customer\Domain\Services;

use App\Modules\Core\Domain\Traits\ServiceTrait;
use App\Modules\Customer\Domain\Models\Address;

class AddressService
{
    use ServiceTrait;

    public function model(): string 
    {
        return Address::class;
    }
        
    public function hasManyRelations(): array
    {
        return [];
    }
}
