<?php

namespace Application\Policies;

use App\Models\User;
use Application\Models\Login;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoginPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Login $login)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Login $login)
    {
        //
    }

    public function delete(User $user, Login $login)
    {
        //
    }

    public function restore(User $user, Login $login)
    {
        //
    }

    public function forceDelete(User $user, Login $login)
    {
        //
    }
}
