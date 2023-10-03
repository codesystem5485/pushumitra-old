<?php

return [
    'status_code' => [
        'not_found' => 204,
        'success' => 200,
        'server_error' => 500,
        'validation_error' => 401
    ],
    'file' => [
        'user_file_path' => 'upload/user',
    ],
    'otp_expiration_min' => 1,
    'entry_fess' => [10,20,50,100],
    'permissions' => [
        'role-list',
        'role-create',
        'role-edit',
        'role-delete',
        'user-list',
        'user-create',
        'user-edit',
        'user-delete',
        'Other',
        'general-setting',
        'log',
    ],
    'twelve_data_api_key' => '2bf0108ad19d4ea0bfb42a25b048d1bb',
    'twelve_data_url' => 'https://api.twelvedata.com'
];
?>