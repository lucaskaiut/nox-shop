<?php

namespace App\Modules\Product\Domain\Models;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\Company\Domain\Scopes\CompanyGlobalScope;
use App\Modules\Product\Domain\Observers\AttributesGroupObserver;
use Database\Factories\AttributesGroupFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(AttributesGroupObserver::class)]
class AttributesGroup extends Model
{
    /** @use HasFactory<\Database\Factories\AttributesGroupFactory> */
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return AttributesGroupFactory::new();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyGlobalScope());
    }
}
