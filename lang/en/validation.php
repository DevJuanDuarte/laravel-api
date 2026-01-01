<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute must be a valid email address.',
    'string' => 'The :attribute must be a string.',
    'max' => [
        'string' => 'The :attribute must not be greater than :max characters.',
    ],
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'email' => 'email',
        'password' => 'password',
        'name' => 'name',
    ],

];
