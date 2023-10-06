<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;
class UserDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','rv_speciality','rv_state_verternity_council','rv_state_verternity_council_no','rv_name_of_working_org','rv_working_village','rv_working_city_town','rv_working_state','rv_working_pincode','pm_collage_name','pm_collage_address','pm_nominee_name','pm_nominee_relationship','pm_nominee_dob','pm_aadhar_no','pm_aadhar_photo_front','pm_aadhar_photo_back','job_type','pm_pan_no','pm_photo','pm_bank_name','pm_account_no','pm_ifsc_code','pm_cheque_photo','pm_name_of_org','created_at','updated_at','deleted_at',
    ]; 
}
