<?php

dataset('configUpgrades', [
    '0.10.0 config' => [
        __DIR__.'/resources/config.0.10.0.php',

        // Expected net result
        [
            'domain_path' => 'src/CustomDomainFolder',
            'domain_namespace' => 'CustomDomainNamespace',
            'namespaces' => [
                'model' => 'CustomModels',
                'data_transfer_object' => 'CustomData',
                'view_model' => 'CustomViewModels',
                'value_object' => 'CustomValueObjects',
                'action' => 'CustomActions',
                'cast' => 'Casts',
                'channel' => 'Channels',
                'command' => 'Commands',
                'enum' => 'Enums',
                'event' => 'Events',
                'exception' => 'Exceptions',
                'factory' => 'Database\Factories',
                'job' => 'Jobs',
                'listener' => 'Listeners',
                'mail' => 'Mail',
                'notification' => 'Notifications',
                'observer' => 'Observers',
                'policy' => 'Policies',
                'provider' => 'Providers',
                'resource' => 'Resources',
                'rule' => 'Rules',
                'scope' => 'Scopes',
            ],
            'base_model' => 'Domain\Shared\Models\CustomBaseModel',
            'base_dto' => 'Spatie\LaravelData\Data',
            'base_view_model' => 'Domain\Shared\ViewModels\CustomViewModel',
            'base_action' => null,
            'autoload' => [
                'providers' => true,
                'commands' => true,
                'policies' => true,
                'factories' => true,
            ],
            'cache_directory' => 'bootstrap/cache/ddd',
        ],
    ],
]);
