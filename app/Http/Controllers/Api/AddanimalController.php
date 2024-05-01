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
use App\Models\Rxreminder;

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
				//'UID_number' => 'nullable|numeric|digits:12',
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
			$aInsertData['user_id'] = $postData['user_id'];
			$aInsertData['UID_number'] = $postData['UID_number'];
			if(isset($postData['species'])){
				$aInsertData['species'] = $postData['species'];
			}
			if(isset($postData['species'])){
				$aInsertData['breed'] = $postData['breed'];
			}
			
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
	
	public function updateAnimal(Request $request){
		$postData = request()->all();
		
		if($postData['name']=='' && $postData['UID_number']=='')
		{
			$errmessage = trans('messages.enter_name_or_uid');
			return $this->sendError([],$errmessage,400);
		}
		
		$validator = Validator::make($postData, [
				'age' => 'required|numeric',
				'sex' => 'required|string',
				
				
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
		$checkAnimalName = Animals::where('name',$postData['name'])->where('animal_owner',$postData['user_id'])->where('id','!=',$postData['edit_id'])->count();
		if($checkAnimalName > 0){
			
			return  $this->sendError([],trans('messages.animal_name_exists'),400);
		}
        $response = [];
		DB::beginTransaction();
		try{   

			$aInsertData['name'] = $postData['name'];
			//$aInsertData['animal_owner'] = $postData['user_id'];
			$aInsertData['description'] = $postData['description'];
			$aInsertData['sex'] = $postData['sex'];
			$aInsertData['age'] = $postData['age'];
			$aInsertData['user_id'] = $postData['user_id'];
			$aInsertData['UID_number'] = $postData['UID_number'];
			if(isset($postData['species'])){
				$aInsertData['species'] = $postData['species'];
			}
			if(isset($postData['breed'])){
				$aInsertData['breed'] = $postData['breed'];
			}
			
            $addAnimal = $this->addAnimalRepo->update($postData['edit_id'],$aInsertData);
			$existing_arr = [];
			if(isset($postData['existing_images'])){
				$existing_arr = $postData['existing_images'];
			}
			$animalImage = AddAnimalImages::where('animal_id',$addAnimal->id)->get();
			
			if(count($animalImage)>0)
			{
				foreach($animalImage as $image)
				{
					if(!in_array($image['image_name'],$existing_arr)){
						$this->removeFile($image->image_name,'animal');
						$image->delete();
						 
					}
				}
			}
			
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
            $message = trans('messages.add_animal_update',['name' => $request->UID_number]);
            storeActicityLog(trans('messages.add_animal_update'),$message,$request->user_id,$addAnimal);
			
           return $this->sendResponse($response,trans('messages.add_animal_update'),200);
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
		$requestData = request()->all();
		/*$response['results']  =   Animals::leftJoin('species', 'species.id', '=', 'animals.species')
			->select( 'animals.*','species.specie as species_name',
            DB::raw('(select image_name from add_animal_images where animal_id  =   animals.id order by id asc limit 1) as image_name')  )
          ->where('animals.animal_owner',$postData['user_id'])
		  ->orderBy('animals.id','ASC')->get();*/
		  
		  $query  = Animals::leftJoin('species', 'species.id', '=', 'animals.species')
			->select( 'animals.*','species.specie as species_name',
            DB::raw('(select image_name from add_animal_images where animal_id  =   animals.id order by id asc limit 1) as image_name')  )
          ->where('animals.animal_owner',$requestData['user_id']);
		  
		  if(isset($requestData['search_input']) && $requestData['search_input']!=''){
			  $words = preg_split("/[\s,]+/", $requestData['search_input'], -1);
			  $query  =$query->where(function($query) use($words){
				foreach($words as $word) {
					$query->where(function($q) use($word){
						$q->where('name', 'LIKE', '%'.$word.'%')
						 ->orWhere('UID_number', 'LIKE', '%'.$word.'%')
						    ->orWhere('breed', 'LIKE', '%'.$word.'%')
							->orWhere('species.specie', 'LIKE', '%'.$word.'%');
					});
				}
			});
		  }
		  $query  = $query->where('animals.status',1)->orderBy('animals.id','DESC')->get(); 
		  $response['results'] =$query;
		  $response['image_base_path'] =  url("/upload/animal")."/";
			
		return $this->sendResponse($response,"",200);
	}
	
	public function deleteAnimal(Request $request){
		$postData = request()->all();
		
		$validator = Validator::make($postData, [
			'id' => 'required',
			'delete_reason' => 'required',
			'delete_note' => 'required',
		]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
	
		$id = $request->id;
        $addAnimal = Animals::where('id',$id)->first();
		if($addAnimal){
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
		
			//$addAnimal->delete();
			$arr = array(
				'status'=>0,
				'delete_reason'=>$request->delete_reason,
				'delete_note'=>$request->delete_note,
			);
			$animals = $this->addAnimalRepo->update($id,$arr);
			$rxreminder = Rxreminder::where('animal_id',$id)->delete();
		}
        $response=[];
        ## Store log
        $message = trans('messages.add-animal_delete',['name' => $id]);
        storeActicityLog(trans('messages.delete'),$message,$postData['user_id'],$addAnimal);
		return $this->sendResponse($response,trans('messages.animal_delete'),200);
        
    }
}
