<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FinancialCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinancialCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FinancialCategory');
    }

    public function view(AuthUser $authUser, FinancialCategory $financialCategory): bool
    {
        return $authUser->can('View:FinancialCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FinancialCategory');
    }

    public function update(AuthUser $authUser, FinancialCategory $financialCategory): bool
    {
        return $authUser->can('Update:FinancialCategory');
    }

    public function delete(AuthUser $authUser, FinancialCategory $financialCategory): bool
    {
        return $authUser->can('Delete:FinancialCategory');
    }

    public function restore(AuthUser $authUser, FinancialCategory $financialCategory): bool
    {
        return $authUser->can('Restore:FinancialCategory');
    }

    public function forceDelete(AuthUser $authUser, FinancialCategory $financialCategory): bool
    {
        return $authUser->can('ForceDelete:FinancialCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FinancialCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FinancialCategory');
    }

    public function replicate(AuthUser $authUser, FinancialCategory $financialCategory): bool
    {
        return $authUser->can('Replicate:FinancialCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FinancialCategory');
    }

}