<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Breeder;
use App\Models\Species;
use Spatie\Permission\Models\Permission;
use App\Repositories\Interfaces\Breeder\BreederRepositoryInterface;
use DB;
use Validator;
use App\Traits\FileUpload;
use App\Models\BreederImages;

class BreederController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $breederRepo;
	
    /**Breeder Construct 
     * @return url 
     */
    public function __construct(BreederRepositoryInterface $breederRepo){
		$this->breederRepo = $breederRepo;
    }

    public function addBreeder(Request $request){
        
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'breeder_name' => 'required',
				//'firm_registration_number' => 'required',
				'mobile_number' => "required|numeric",
				'animal_breed' => 'required',
				'animal_description' => 'required',
				'age' => 'required|numeric',
				'vaccination_done' => 'required',
				'expected_price' => 'required',
				'address' => 'required',
				'state' => 'required',
				'state_id'=>'required',
				'city_town'=>'required',
				'taluka'=>'nullable|string',
				'district'=>'nullable|string',
				'pincode'=>'required|numeric',
				'pm_code'=>'required|numeric',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $breeder = $this->breederRepo->create($aInsertData);
			
			if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'breederanimals');
                    if($fileName)
                    {
                        $animalImage = BreederImages::create(['breeder_id'=>$breeder->id,'image_name' => $fileName]);
                    }
                } 
            }
            
            DB::commit();
			 ## Store log
            $message = trans('messages.breeder_create',['name' => $breeder->breeder_name]);
            storeActicityLog(trans('messages.breeder_create'),$message);
			return $this->sendResponse($response,$message,200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getBreederList(Request $request)
	{
		$response['breeders']  =   Breeder::select( 'breeders.*',
            DB::raw('(select image_name from  breeder_images where breeder_id  = breeders.id order by id asc limit 1) as image_name'))
           ->orderBy('breeders.id','ASC')->get();
		   $response['breeder_image_path'] =  url("/upload/breederanimals/");
			
		return $this->sendResponse($response,"",200);
	}
	
	public function breederDetail(Request $request)
	{
		$id = $request->breeder_id;
		$breeder = Breeder::leftJoin('breeds', 'breeds.id', '=', 'breeders.animal_breed')
		->select('breeders.*','breeds.breed as animal_breed')
		->where('breeders.id',$id)
		->first();
		
		$breederimages=array();
		
		if($breeder){
			$breederimages = BreederImages::where('breeder_id',$breeder->id)->get();
			$details = array('breederDetails'=>$breeder,'breederImages' =>$breederimages);
			$details['breeder_image_path'] =  url("/upload/breederanimals/");
			
			return $this->sendResponse($details,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }
}
