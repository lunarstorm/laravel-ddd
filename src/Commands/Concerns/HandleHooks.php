<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

trait HandleHooks
{
    protected function beforeHandle()
    {
        //
    }

    protected function afterHandle()
    {
        //
    }

    /**
     * Handle the command, with before and after hooks.
     * 
     * @return bool|null
     */
    public function handle()
    {
        $this->beforeHandle();

        parent::handle();

        $this->afterHandle();

        return true;
    }
}
