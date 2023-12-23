<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Animals;
use App\Models\AddAnimalImages;
use App\Models\Breeds;
use App\Models\Species;
use App\Models\User;
use App\Http\Requests\AddanimalProcessRequest;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\Addanimal\AddanimalRepositoryInterface;
use DB;
use Validator;
use App\Traits\FileUpload;

class AddanimalController extends BaseController
{
    use FileUpload;
    protected $url = '';
    protected $userRepo;
    protected $addAnimalRepo;
    /**
     * Addanimal Construct 
     * @return url 
     */
    public function __construct(UserRepositoryInterface $userRepo,AddanimalRepositoryInterface $addAnimalRepo){
        $this->userRepo = $userRepo;
        $this->addAnimalRepo = $addAnimalRepo;
    }
   
    /**
     * Store Animal
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function addAnimal(Request $request){
		$postData = request()->all();
		
		if($postData['name']=='' && $postData['UID_number']=='')
		{
			$errmessage = trans('messages.enter_name_or_uid');
			return $this->sendError([],$errmessage,400);
		}
		
		$validator = Validator::make($postData, [
				'UID_number' => 'nullable|numeric|digits:12',
				//'name' => 'required',
				//'species' => 'required',
				//'breed' => "required",
				'user_id' => "required",
				'age' => 'required|numeric',
				'sex' => 'required|string',
				//'description' => 'required',
				//'animal_owner_id' => 'required',
				
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$checkAnimalName = Animals::where('name',$postData['name'])->where('animal_owner',$postData['user_id'])->count();
		if($checkAnimalName > 0){
			
			return  $this->sendError([],trans('messages.animal_name_exists'),400);
		}
        
        DB::beginTransaction();
		$response = [];
        try{      
					
            $aInsertData['name'] = $postData['name'];
			$aInsertData['animal_owner'] = $postData['user_id'];
			$aInsertData['description'] = $postData['description'];
			$aInsertData['sex'] = $postData['sex'];
			$aInsertData['age'] = $postData['age'];
			//$aInsertData['species'] = $postData['species'];
			//$aInsertData['breed'] = $postData['breed'];
			$aInsertData['user_id'] = $postData['user_id'];
			$aInsertData['UID_number'] = $postData['UID_number'];
			
            $addAnimal = $this->addAnimalRepo->create($aInsertData);
            if($request->animal_photo)
            {
                foreach($request->animal_photo as $photo)
                {
                    $fileName ='';
                    $fileName = $this->uploadFile($photo,'animal');
                    if($fileName)
                    {
                        AddAnimalImages::create(['animal_id'=>$addAnimal->id,'image_name' => $fileName]);
                    }
                }
            }

            DB::commit();
            ## Store log
            $message = trans('messages.add_animal_create',['name' => $request->UID_number]);
            storeActicityLog(trans('messages.add_animal_create'),$message,$request->user_id,$addAnimal);
			
           return $this->sendResponse($response,trans('messages.add_animal_create'),200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
           
            return  $this->sendError($response,trans('messages.something'),500);
        }
    }
	
	public function getAnimalList(Request $request)
	{
		$postData = request()->all();
		$response['animals']  =   Animals::select( 'animals.*',
            DB::raw('(select image_name from add_animal_images where animal_id  =   animals.id order by id asc limit 1) as image_name')  )
          ->where('animals.animal_owner',$postData['user_id'])
		  ->orderBy('animals.id','ASC')->get();
		  $response['animal_image_path'] =  url("/upload/animal/");
			
		return $this->sendResponse($response,"",200);
	}
	
	public function deleteAnimal(Request $request){
		$postData = request()->all();		
		$validator = Validator::make($postData, [
			'animal_id' => 'required',
		]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
	
		$id = $request->animal_id;
        $addAnimal = Animals::where('id',$id)->first();
	
        $animalImage = AddAnimalImages::where('animal_id',$addAnimal->id)->get();
        // dd($animalImage);
        if(count($animalImage)>0)
        {
            foreach($animalImage as $image)
            {
                $this->removeFile($image->image_name,'animal');
                $image->delete();
            }
        }
	
        $addAnimal->delete();
        $response=[];
        ## Store log
        $message = trans('messages.add-animal_delete',['name' => $addAnimal->id]);
        storeActicityLog(trans('messages.delete'),$message,$postData['user_id'],$addAnimal);
		return $this->sendResponse($response,trans('messages.add_animal_create'),200);
        
    }
}
