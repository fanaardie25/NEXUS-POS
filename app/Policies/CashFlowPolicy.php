<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CashFlow;
use Illuminate\Auth\Access\HandlesAuthorization;

class CashFlowPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CashFlow');
    }

    public function view(AuthUser $authUser, CashFlow $cashFlow): bool
    {
        return $authUser->can('View:CashFlow');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CashFlow');
    }

    public function update(AuthUser $authUser, CashFlow $cashFlow): bool
    {
        return $authUser->can('Update:CashFlow');
    }

    public function delete(AuthUser $authUser, CashFlow $cashFlow): bool
    {
        return $authUser->can('Delete:CashFlow');
    }

    public function restore(AuthUser $authUser, CashFlow $cashFlow): bool
    {
        return $authUser->can('Restore:CashFlow');
    }

    public function forceDelete(AuthUser $authUser, CashFlow $cashFlow): bool
    {
        return $authUser->can('ForceDelete:CashFlow');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CashFlow');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CashFlow');
    }

    public function replicate(AuthUser $authUser, CashFlow $cashFlow): bool
    {
        return $authUser->can('Replicate:CashFlow');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CashFlow');
    }

}