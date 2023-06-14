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
        'domains' => 'src/Domain',
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
    | e.g., Domain/Invoicing/Models/*
    |       Domain/Invoicing/Data/*
    |       Domain/Invoicing/ViewModels/*
    |       Domain/Invoicing/ValueObjects/*
    |       Domain/Invoicing/Actions/*
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

        //
        // Actions
        //
        'actions' => 'Actions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Base Model
    |--------------------------------------------------------------------------
    |
    | The base class which generated domain models should extend. By default,
    | generated domain models will extend `Domain\Shared\Models\BaseModel`,
    | which will be created if it doesn't already exist.
    |
    */
    'base_model' => 'Domain\Shared\Models\BaseModel',

    /*
    |--------------------------------------------------------------------------
    | Base DTO
    |--------------------------------------------------------------------------
    |
    | The base class which generated data transfer objects should extend. By
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
    | The base class which generated view models should extend. By default,
    | generated domain models will extend `Domain\Shared\ViewModels\BaseViewModel`,
    | which will be created if it doesn't already exist.
    |
    */
    'base_view_model' => 'Domain\Shared\ViewModels\ViewModel',

    /*
    |--------------------------------------------------------------------------
    | Base Action
    |--------------------------------------------------------------------------
    |
    | The base class which generated action objects should extend. By default,
    | generated actions are based on the `lorisleiva/laravel-actions` package
    | and do not extend anything.
    |
    */
    'base_action' => null,
];
