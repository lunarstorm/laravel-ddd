<?php

namespace Domain\Invoicing\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Domain\Invoicing\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Invoice $invoice)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Invoice $invoice)
    {
        //
    }

    public function delete(User $user, Invoice $invoice)
    {
        //
    }

    public function restore(User $user, Invoice $invoice)
    {
        //
    }

    public function forceDelete(User $user, Invoice $invoice)
    {
        //
    }
}
