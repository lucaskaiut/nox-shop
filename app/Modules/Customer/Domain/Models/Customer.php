<?php

namespace App\Modules\Customer\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\Customer\Domain\Observers\CustomerObserver;
use Database\Factories\CustomerFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy(CustomerObserver::class)]
class Customer extends User
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasApiTokens, HasFactory, CanResetPassword;
    
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

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    protected static function newFactory()
    {
        return CustomerFactory::new();
    }

    protected static function booted()
    {
        static::addGlobalScope('customer_owner', function (Builder $builder) {
            $user = auth('sanctum')->user();

            if ($user instanceof Customer) {
                $builder->where('id', $user->id);
            }
        });
    }
}
