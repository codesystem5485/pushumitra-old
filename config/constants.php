<?php

return [
    'status_code' => [
        'not_found' => 204,
        'success' => 200,
        'server_error' => 500,
        'validation_error' => 401
    ],
    'file' => [
        'product_file_path'=>'upload/product',
        'animal_file_path'=>'upload/animal',
        'chemist_file_path'=>'upload/chemist',
        'vehicle_file_path'=>'upload/vehicle',
        'productsale_file_path'=>'upload/productsale',
        'animalsale_file_path'=>'upload/animalsale',
		'breederanimal_file_path'=>'upload/breederanimals',
		'recommendationletter_file_path'=>'upload/recommendation_letter',
        'profile_photo_file_path' => 'upload/profile_photo',
        'book_file_path' => 'upload/book',
        'education_certificate_file_path'=>'upload/education_certificate',
        'aadhar_photo_front_file_path'=>'upload/aadhar_photo_front',
        'aadhar_photo_back_file_path'=>'upload/aadhar_photo_back',
        'pan_photo_file_path'=>'upload/pan_photo',
        'cheque_photo_file_path'=>'upload/cheque_photo',
    ],
    'otp_expiration_min' => 5,
    'entry_fess' => [10,20,50,100],
	'age_array' => ['0-6 months','6-12 months','1-3 years','3 & above'],
	'jobtype_array' => ['Private','Public','Unemployed'],
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