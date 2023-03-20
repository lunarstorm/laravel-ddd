<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | This value contains paths to the layers of the application in the context
    | of domain driven design, relative to the base folder of the application.
    |
    */

    'paths' => [
        //
        // Path to the Domain layer.
        //
        'domains' => 'src/Domains',

        //
        // Path to modules in the application layer. This is an extension of
        // domain driven design applied to the application layer, bundling
        // application objects (Controllers, Resources, Requests) in a
        // more modular fashion.
        //
        // e.g., app/Modules/Invoicing/Controllers/*
        //       app/Modules/Invoicing/Resources/*
        //       app/Modules/Invoicing/Requests/*
        //
        'modules' => 'app/Modules',
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain Object Namespaces
    |--------------------------------------------------------------------------
    |
    | This value contains the default namespaces of generated domain
    | objects relative to the domain namespace of which the object
    | belongs to.
    |
    | e.g., Domains/Invoicing/Models/*
    |       Domains/Invoicing/Data/*
    |       Domains/Invoicing/ViewModels/*
    |       Domains/Invoicing/ValueObjects/*
    |
    */
    'namespaces' => [
        //
        // Models
        //
        'models' => 'Models',

        //
        // Data Transfer Objects (DTO)
        //
        'data_transfer_objects' => 'Data',

        //
        // View Models
        //
        'view_models' => 'ViewModels',

        //
        // Value Objects
        //
        'value_objects' => 'ValueObjects',
    ],

    /*
    |--------------------------------------------------------------------------
    | Base Model
    |--------------------------------------------------------------------------
    |
    | This base model which generated domain models should extend. By default,
    | generated domain models will extend `Domains\Shared\Models\BaseModel`,
    | which will be created if it doesn't already exist.
    |
    */
    'base_model' => 'Domains\Shared\Models\BaseModel',

    /*
    |--------------------------------------------------------------------------
    | Base DTO
    |--------------------------------------------------------------------------
    |
    | This base model which generated data transfer objects should extend. By
    | default, generated DTOs will extend `Spatie\LaravelData\Data` from
    | Spatie's Laravel-data package, a highly recommended data object
    | package to work with.
    |
    */
    'base_dto' => 'Spatie\LaravelData\Data',

    /*
    |--------------------------------------------------------------------------
    | Base ViewModel
    |--------------------------------------------------------------------------
    |
    | This base view model which generated view models should extend. By default,
    | generated domain models will extend `Domains\Shared\ViewModels\BaseViewModel`,
    | which will be created if it doesn't already exist.
    |
    */
    'base_view_model' => 'Domains\Shared\ViewModels\ViewModel',
];
