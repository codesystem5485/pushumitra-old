<?php
namespace App\Services;
ini_set('memory_limit', '-1');
class BaseService{
    /**
     * construct function
     * 
     */
    public function __construct(){
    
    }

    /**
     * Get curl call for external url
     * @param url $url,string $method
     * @return JSON
     */
    public function curlMethod($url,$method = 'GET'){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYPEER =>false,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => $method
        ));
        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $oErrorMsg = curl_error($curl);
        }
        curl_close($curl);
        if(isset($oErrorMsg)) {
            return ['status' => false,'data' => []];
        }else{
            return ['status' => true,'data' => json_decode($response,true)];
        }
    }
}