<?php

namespace Lunarstorm\LaravelDDD\Commands\Concerns;

trait OverridesHandle
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
