<?php

namespace App\Modules\Company\Domain\Policies;

use App\Modules\Company\Domain\Models\Company;
use App\Modules\User\Domain\Models\User;

class CompanyPolicy
{
    public function update(User $user, Company $company): bool
    {
        return $user->company_id == $company->id;
    }

    public function show(User $user, Company $company): bool
    {
        return $user->company_id == $company->id;
    }
}
