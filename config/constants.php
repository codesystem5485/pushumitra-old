<?php

return [
    'status_code' => [
        'not_found' => 204,
        'success' => 200,
        'server_error' => 500,
        'validation_error' => 401
    ],
    'file' => [
        'animal_file_path'=>'upload/animal',
        'chemist_file_path'=>'upload/chemist',
        'vehicle_file_path'=>'upload/vehicle',
        'productsale_file_path'=>'upload/productsale',
        'animalsale_file_path'=>'upload/animalsale',
        'profile_photo_file_path' => 'upload/profile_photo',
        'book_file_path' => 'upload/book',
        'education_certificate_file_path'=>'upload/education_certificate',
        'aadhar_photo_front_file_path'=>'upload/aadhar_photo_front',
        'aadhar_photo_back_file_path'=>'upload/aadhar_photo_back',
        'pan_photo_file_path'=>'upload/pan_photo',
        'cheque_photo_file_path'=>'upload/cheque_photo',
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