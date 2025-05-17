<?php

namespace App\Modules\Company\Domain\Singletons;

use App\Modules\Company\Domain\Models\Company as CompanyModel;

class Company
{
    private CompanyModel $company;

    public function registerCompany(CompanyModel $company)
    {
        $this->company = $company;
    }

    public function company(): CompanyModel
    {
        return $this->company;
    }
}