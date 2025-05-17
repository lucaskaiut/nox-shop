<?php

namespace App\Modules\Customer\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\Customer\Domain\Observers\CustomerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy(CustomerObserver::class)]
class Customer extends Authenticatable
{
    use HasApiTokens;
    
    protected $guarded = [];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
