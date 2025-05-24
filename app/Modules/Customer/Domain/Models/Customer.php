<?php

namespace App\Modules\Customer\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\Customer\Domain\Observers\CustomerObserver;
use App\Modules\User\Domain\Models\User;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(CustomerObserver::class)]
class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;
    
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
            /** @var User $user */
            $user = auth('sanctum')->user();

            if (!$user) {
                return;
            }

            $customerId = Customer::withoutGlobalScopes()->where('user_id', $user->id)->value('id');

            if ($customerId) {
                $builder->where('id', $customerId);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
