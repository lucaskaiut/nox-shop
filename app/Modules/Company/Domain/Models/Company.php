<?php

namespace App\Modules\Company\Domain\Models;

use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function getSetting(string $key, ?string $type = null, mixed $default = null): mixed
    {
        $query = $this->settings()->where('key', $key);

        if ($type) {
            $query->where('type', $type);
        }

        /** @var Setting $setting */
        $setting = $query->first();

        return $setting?->value ?? $default;
    }

    public function getSettingsByType(string $type): array
    {
        return $this->settings()
            ->where('type', $type)
            ->pluck('value', 'key')
            ->toArray();
    }
}
