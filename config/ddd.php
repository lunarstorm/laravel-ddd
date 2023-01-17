<?php

return [
    'paths' => [
        // Path to the Application layer
        'application' => 'app/Modules',

        // Path to the Domain layer
        'domains' => 'src/Domains',

        // Relative paths of domain objects
        'dataTransferObjects' => 'Data',
        'models' => 'Models',
        'viewModels' => 'ViewModels',

        // Relative paths of domain-application objects
        'requests' => 'Requests',
        'resources' => 'Resources',
        'controllers' => 'Controllers',
    ],

    'namespaces' => [
        // Domain Layer
        'models' => 'Models',
        'data_transfer_objects' => 'Data',
        'view_models' => 'ViewModels',

        // Application Layer
        'requests' => 'Requests',
        'resources' => 'Resources',
        'controllers' => 'Controllers',
    ]
];
