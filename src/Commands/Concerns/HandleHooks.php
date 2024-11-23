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

    public function handle()
    {
        $this->beforeHandle();

        parent::handle();

        $this->afterHandle();
    }
}
