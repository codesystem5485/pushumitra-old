<?php
use App\Models\User;
if(!function_exists('convert_permission_name')){ 
    function convert_permission_name(string $string = ''){
        return strtoupper(preg_replace("/[^a-zA-Z]+/", " ", $string));
    }
}

if(!function_exists('random_number')){ 
    function random_number(){
        return mt_rand(1111,9999);
    }
}

if(!function_exists('convert_small')){ 
    function convert_small($value){
        return strtolower($value);
    } 
}

if(!function_exists('skip_empty_field')){ 
    function skip_empty_field($data){
        if(!empty($data)){
            return array_filter($data);
        }
    }
}
if(!function_exists('encryptData')){ 
    function encryptData($id = ''){
        $encryptKey = base64_encode($id); 
        if($encryptKey){
            return $encryptKey;
        }
        return false;
    }
}
if(!function_exists('decryptData')){ 
    function decryptData($id = ''){
        $decryptKey = base64_decode($id); 

        if($decryptKey){
            return $decryptKey;
        }
        return false;
    }
}
if(!function_exists('storeActicityLog')){  
    function storeActicityLog($log_name = 'default', $msg='',$causered = [],$performed = [],$properties = []){
        $oActivity = activity($log_name);
        if(!empty($causered)){
            $oActivity = $oActivity->causedBy($causered);
        }
        if(!empty($performed)){
            $oActivity = $oActivity->performedOn($performed);
        }
        $oActivity->withProperties($properties)
        ->log($msg);
    }
}

if(!function_exists('convertDate')){ 
    function convertDate($date = '', $format = 'd-m-Y H:i:s'){
       return date($format,strtotime($date));
    }
}

if(!function_exists('getRoleById')){ 
    function getRoleById($nId){
       $nUserData = User::where('id',$nId)->first();
       return !empty($nUserData->roles[0]->name) ? $nUserData->roles[0]->name : null; 
    }
}

if(!function_exists('generateRoomCode')){ 
    function generateRoomCode()
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $sString =  substr(str_shuffle($str_result),0, 7);
        $count = modelname::where('room_code',trim($sString))->count();
        if($count>0){
            generateRoomCode();
        }
        return $sString;
    }
}

if(!function_exists('calculatePrizePool')){ 
    function calculatePrizePool($nEntryFee,$nRoomSize)
    {
        $dTotalValue = $nEntryFee * $nRoomSize;
        $nPercentAmount = ($dTotalValue * 10) / 100;
        $dPrizePool = $dTotalValue - $nPercentAmount;
        return round($dPrizePool);
    }
}
