<?php

namespace App\Modules\Customer\Domain\Models;

use App\Modules\Customer\Domain\Observers\AddressObserver;
use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(AddressObserver::class)]
class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;
    
    protected $guarded = [];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('address_owner', function (Builder $builder) {
            $user = auth('sanctum')->user();

            if ($user instanceof Customer) {
                $builder->where('customer_id', $user->id);
            }
        });
    }

    protected static function newFactory()
    {
        return AddressFactory::new();
    }
}
