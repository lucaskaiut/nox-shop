<?php

namespace App\Modules\Company\Domain\Traits;

use App\Modules\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Model;

trait OwnerPolicyTrait
{
    public function create(Model $model): bool
    {
        return true;
    }

    public function update(User $user, Model $model): bool
    {
        return $user->company_id == $model->company_id;
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->company_id == $model->company_id;
    }

    public function show(User $user, Model $model): bool
    {
        return $user->company_id == $model->company_id;
    }

    public function viewAny(Model $model): bool
    {
        return true;
    }
}
