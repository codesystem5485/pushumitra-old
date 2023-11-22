<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\AnimalForSale;
use App\Models\AnimalImages;
use App\Models\Breeds;
use App\Models\Species;
use App\Models\AnimalType;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\AnimalsaleProcessRequest;
use App\Repositories\Interfaces\Animalsale\AnimalsaleRepositoryInterface;
use DB;
use Validator;

use App\Traits\FileUpload;

class AnimalsaleController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $animalsaleRepo;
    /**
     * Animal Sale Construct 
     * @return url 
     */
    public function __construct(AnimalsaleRepositoryInterface $animalsaleRepo){
		$this->animalsaleRepo = $animalsaleRepo;
    } 

    public function addAnimalForSale(Request $request){
        
		$postData = request()->all();
		$validator = Validator::make($postData, [
				'UID_number' => 'required',
				'species' => 'required',
				'breed' => "required",
				//'type' => "required",
				'age' => 'required|numeric',
				'sex' => 'required|string',
				'price' => 'required|numeric',
				'description' => 'required',
				'address' => 'required',
				'contact_number_of_owner' => 'required|numeric|min:10',
				'contact_name_of_owner' => 'required',
				'pm_code'=>'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
            $animalsale = $this->animalsaleRepo->create($aInsertData);
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'animalsale');
                    if($fileName)
                    {
                        $animalImage = AnimalImages::create(['animal_sale_id'=>$animalsale->id,'image_name' => $fileName]);
                    }
                } 
            }
            
            DB::commit();
			 ## Store log
            $message = trans('messages.animalsale_create',['name' => $request->UID_number]);
            storeActicityLog(trans('messages.animalsale_create'),$message);
			
			
			return $this->sendResponse($response,trans('messages.animalsale_create'),200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
    }
	
	public function getAnimalSaleList(Request $request)
	{
		$response['animalsale']  =   Animalforsale::select( 'animal_for_sales.*',
            DB::raw('(select image_name from animal_images where animal_sale_id  =   animal_for_sales.id order by id asc limit 1) as image_name')  )
           ->orderBy('animal_for_sales.id','ASC')->get();
		   $response['animalsale_image_path'] =  url("/upload/animalsale/");
			
		return $this->sendResponse($response,"",200);
	}
	
	public function animalSaleDetail(Request $request){
		$id = $request->animalsale_id;
        $animalsale = Animalforsale::find($id);
        $animalimages = AnimalImages::where('animal_sale_id',$animalsale->id)->get();
		if($animalsale){
			$details = array('animalSaleDetails'=>$animalsale,'animalSaleImages' =>$animalimages);
			$details['animalsale_image_path'] =  url("/upload/animalsale/");
			return $this->sendResponse($details,trans('messages.records_found'));
        }else{
            return  $this->sendError([],trans('messages.records_not_found'),404); 
        }
    }

    /**
     * Delete Animal for sale
     * @param int $id (Animal for sale Id)
     * @return Route
     */
    public function delete($id){ 
        $animalsale = Animalforsale::where('id',$id)->first();
        $animalImages = AnimalImages::where('animal_sale_id',$id)->get();
        if($animalImages)
        {
            if(count($animalImages)>0)
            {
                foreach($animalImages as $image)
                {
                    $this->removeFile($image->image_name,'animalsale');
                }
            }
        }
        $animalImages = AnimalImages::where('animal_sale_id',$id)->delete();
        // $animalImages->delete();
        $animalsale->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.animalsale_delete',['name' => $animalsale->UID_number]);
        storeActicityLog(trans('messages.animalsale_delete'),$message,Auth::user(),$animalsale);
        return redirect()->route('animal-sale.index');
    }

    public function removeImage($id)
    {
        $animalImage = AnimalImages::where('id',$id)->first();
        $this->removeFile($animalImage->image_name,'animalsale');
        $animalImage->delete();
        // Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.animalsale_remove',['name' => $animalImage->id]);
        storeActicityLog(trans('messages.animalsale_remove'),$message,Auth::user(),$animalImage);
        // return redirect()->route('animal-sale.edit',$animalImage->id);
        return true;
    }

}
