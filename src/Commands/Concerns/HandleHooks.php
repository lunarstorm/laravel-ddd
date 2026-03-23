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
     * @return int|bool|null
     */
    public function handle()
    {
        $this->beforeHandle();

        /** @phpstan-ignore-next-line staticMethod.void */
        $result = parent::handle();

        $this->afterHandle();

        // Handle various return types from parent commands
        /** @phpstan-ignore-next-line identical.alwaysFalse */
        if ($result === false) {
            return self::FAILURE;
        }

        /** @phpstan-ignore-next-line function.impossibleType */
        if (is_int($result)) {
            return $result;
        }

        // void/null defaults to SUCCESS
        return self::SUCCESS;
    }
}
