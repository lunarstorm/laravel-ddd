<?php

return [
    'domain_path' => 'src/CustomDomainFolder',
    'domain_namespace' => 'CustomDomainNamespace',
    'application' => [
        'objects' => [
            'keepthis',
        ],
    ],
    'namespaces' => [
        'model' => 'CustomModels',
        'data_transfer_object' => 'CustomData',
        'view_model' => 'CustomViewModels',
        'value_object' => 'CustomValueObjects',
        'action' => 'CustomActions',
    ],
    'base_model' => 'Domain\Shared\Models\CustomBaseModel',
    'base_dto' => 'Spatie\LaravelData\Data',
    'base_view_model' => 'Domain\Shared\ViewModels\CustomViewModel',
    'base_action' => null,
    'autoload' => [
        'migrations' => false,
    ],
];
