<?php

namespace App\Modules\Company\Domain\Services;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\Core\Domain\Traits\ServiceTrait;

class CompanyService
{
    use ServiceTrait;

    public function model(): string 
    {
        return Company::class;
    }
        
    public function hasManyRelations(): array
    {
        return [];
    }
}
