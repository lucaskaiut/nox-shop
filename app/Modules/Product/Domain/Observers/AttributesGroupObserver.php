<?php

namespace App\Modules\Product\Domain\Observers;

use App\Modules\Product\Domain\Models\AttributesGroup;

class AttributesGroupObserver
{
    public function creating(AttributesGroup $attributesGroup)
    {
        $attributesGroup->company_id = app('company')->company()->id;
    }
}
