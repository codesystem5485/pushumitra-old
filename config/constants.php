<?php

return [
    'status_code' => [
        'not_found' => 204,
        'success' => 200,
        'server_error' => 500,
        'validation_error' => 401
    ],
    'file' => [
        'book_file_path' => 'upload/book',
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
    ],
    'twelve_data_api_key' => '',
    'twelve_data_url' => 'https://api.twelvedata.com'
];
?>