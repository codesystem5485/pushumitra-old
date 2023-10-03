<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Lcobucci\JWT\Parser as JwtParser;
use DB;
use App\Models\User;
use Config;
use File; 
trait FileUpload {

    public function uploadFile($file,$type){
        switch($type){
            case 'user':
            $path = Config::get('constants.file.user_file_path');
            break;
            default:
            $path = '';    
        }
        if(!empty($file)){
            $fileName = time().'-'.$type.'.'.$file->extension();
            $file->move(public_path($path), $fileName);
            return $fileName;
        }
        return false;
    }
    public function removeFile($file,$type){
        
        switch($type){
            case 'user':
            $path = Config::get('constants.file.user_file_path');
            break;
            
            default:
            $path = '';    
        }
        if(!empty($file) && File::exists(public_path($path.'/'.$file))){
            unlink(public_path($path.'/'.$file)); 
        }
    }
}