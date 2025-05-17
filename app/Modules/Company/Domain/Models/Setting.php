<?php

namespace App\Modules\Company\Domain\Models;

use App\Modules\Company\Domain\Scopes\CompanyGlobalScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $guarded = [];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyGlobalScope());
    }
}
