<?php

namespace Infrastructure\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Infrastructure\Models\AppSession;

class AppSessionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, AppSession $appSession)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, AppSession $appSession)
    {
        //
    }

    public function delete(User $user, AppSession $appSession)
    {
        //
    }

    public function restore(User $user, AppSession $appSession)
    {
        //
    }

    public function forceDelete(User $user, AppSession $appSession)
    {
        //
    }
}
