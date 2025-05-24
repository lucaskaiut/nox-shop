<?php

namespace App\Modules\Product\Domain\Services;

use App\Modules\Core\Domain\Traits\ServiceTrait;
use App\Modules\Product\Domain\Models\AttributesGroup;

class AttributesGroupService
{
    use ServiceTrait;

    public function model(): string 
    {
        return AttributesGroup::class;
    }
        
    public function hasManyRelations(): array
    {
        return [];
    }
}
