<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Lcobucci\JWT\Parser as JwtParser;
use DB;
use App\Models\Books;
use Config;
use File; 
trait FileUpload {

    public function uploadFile($file,$type){
        switch($type){
            case 'product':
            $path = Config::get('constants.file.product_file_path');
            break; 
            
            case 'animal':
            $path = Config::get('constants.file.animal_file_path');
            break; 

            case 'chemist':
            $path = Config::get('constants.file.chemist_file_path');
            break; 

            case 'vehicle':
            $path = Config::get('constants.file.vehicle_file_path');
            break; 

            case 'productsale':
            $path = Config::get('constants.file.productsale_file_path');
            break; 

            case 'animalsale':
            $path = Config::get('constants.file.animalsale_file_path');
            break;

			case 'breederanimals':
            $path = Config::get('constants.file.breederanimal_file_path');
            break;
			
			case 'recommendation_letter':
            $path = Config::get('constants.file.recommendationletter_file_path');
            break;

            case 'profile_photo':
            $path = Config::get('constants.file.profile_photo_file_path');
            break;

            case 'book':
            $path = Config::get('constants.file.book_file_path');
            break;

            case 'education_certificate':
                $path = Config::get('constants.file.education_certificate_file_path');
            break;

            case 'aadhar_photo_front':
                $path = Config::get('constants.file.aadhar_photo_front_file_path');
            break;

            case 'aadhar_photo_back':
                $path = Config::get('constants.file.aadhar_photo_back_file_path');
            break;

            case 'pan_photo':
                $path = Config::get('constants.file.pan_photo_file_path');
            break;

            case 'cheque_photo':
                $path = Config::get('constants.file.cheque_photo_file_path');
            break;
            
            default:
            $path = '';    
        }
        if(!empty($file)){
            $fileName = rand(10,100).time().'-'.$type.'.'.$file->extension();
            $file->move(public_path($path), $fileName);
            return $fileName;
        }
        return false;
    }
    public function removeFile($file,$type){
        
        switch($type){
            case 'product':
            $path = Config::get('constants.file.product_file_path');
            break; 

            case 'animal':
            $path = Config::get('constants.file.animal_file_path');
            break; 
            
            case 'chemist':
            $path = Config::get('constants.file.chemist_file_path');
            break; 

            case 'vehicle':
            $path = Config::get('constants.file.vehicle_file_path');
            break; 
            
            case 'productsale':
            $path = Config::get('constants.file.productsale_file_path');
            break; 
            
            case 'animalsale':
            $path = Config::get('constants.file.animalsale_file_path');
            break; 
			
			case 'breederanimals':
            $path = Config::get('constants.file.breederanimal_file_path');
            break;

            case 'book':
            $path = Config::get('constants.file.book_file_path');
            break;
            
            case 'education_certificate':
                $path = Config::get('constants.file.education_certificate_file_path');
            break;

            case 'pm_aadhar_photo_front':
                $path = Config::get('constants.file.pm_aadhar_photo_front_file_path');
            break;

            case 'pm_aadhar_photo_back':
                $path = Config::get('constants.file.pm_aadhar_photo_back_file_path');
            break;

            case 'pm_pan_photo':
                $path = Config::get('constants.file.pm_pan_photo_file_path');
            break;

            case 'pm_cheque_photo':
                $path = Config::get('constants.file.pm_cheque_photo_file_path');
            break;

            default:
            $path = '';    
        }
        if(!empty($file) && File::exists(public_path($path.'/'.$file))){
            unlink(public_path($path.'/'.$file)); 
        }
    }
}