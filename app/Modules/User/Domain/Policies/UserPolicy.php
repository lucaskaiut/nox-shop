<?php

namespace App\Modules\User\Domain\Policies;

use App\Modules\Company\Domain\Traits\OwnerPolicyTrait;

class UserPolicy
{
    use OwnerPolicyTrait;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
}
