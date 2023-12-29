<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonials;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;
use App\Traits\FileUpload;
//use Image;


class TestimonialController extends Controller
{
    use FileUpload;
    protected $url = '';
   
    /**
     * Testimonials Type Construct 
     * @return url 
     */
    public function __construct(){

        $this->middleware('permission:testimonial-list|testimonial-create|testimonial-edit|testimonial-delete', ['only' => ['index','show']]);
        $this->middleware('permission:testimonial-create', ['only' => ['create','store']]);
        $this->middleware('permission:testimonial-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:testimonial-delete', ['only' => ['delete']]);
        
       $this->url = [   
            'listUrl' => route('testimonials.index'),
            'createUrl' => route('testimonials.create')
        ];
    } 

    /**
     * testimonials List
     * @return View
     */
    public function index(){
        $testimonials = Testimonials::where('status',1)->orderBy('id','DESC')->get();
        return view('backend.testimonials.index',['testimonials'=>$testimonials,'url' => $this->url]); 
    }

    /**
     * Add Testimonials View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.testimonials.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Testimonials
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
		$postData = $request->all();
        $this->validate($request, [
            'testimonial_name' => 'required',            
            'testimonial_designation' => 'required', 
			'testimonial_message' => 'required',
			'testimonial_photo' => 'mimes:jpeg,jpg,png', 			
        ]);
		
		DB::beginTransaction();
        try{

			$testimonial_profile_photo='';
			
			
			if(!empty($request->testimonial_photo))
			{
				$testimonial_profile_photoname = $this->uploadFile($request->testimonial_photo,'testimonials');
				if(!empty($testimonial_profile_photoname))
				{
					$testimonial_profile_photo = $testimonial_profile_photoname;
				}
				
				/*$fileName = rand(10,100).time().'-'.$type.'.'.$file->extension();
				$file->move(public_path($path), $fileName);
			
				
				$image = $request->file('testimonial_photo');
				$testimonial_profile_photo = time().'.'.$image->extension();
			 
				$destinationPath = public_path('/testimonials');
				$img = Image::make($image->path());
				$img->resize(100, 100, function ($constraint) {
					$constraint->aspectRatio();
				})->save($destinationPath.'/'.$testimonial_profile_photo);
		   
				//$destinationPath = public_path('/testimonials');
				//$image->move($destinationPath, $input['imagename']);*/
		
			}
			
			$testimonials = new Testimonials();
            $testimonials->testimonial_name = $request->input('testimonial_name');
			$testimonials->testimonial_designation = $request->input('testimonial_designation');
			$testimonials->testimonial_message = $request->input('testimonial_message');
			$testimonials->testimonial_photo = $testimonial_profile_photo;
			$testimonials->user_id = Auth::user()->id;
			$testimonials->save();
            
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store logbbok
            $message = trans('messages.testimonials_create',['name' => $request->input('testimonial_name')]);
            storeActicityLog(trans('messages.testimonials_create'),$message,Auth::user(),$testimonials);
            return redirect()->route('testimonials.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('testimonials.index');   
            
        }
     
    }

    /**
     * Get Particular testimonials
     * @param int $id (testimonials Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $testimonials = Testimonials::find($id);
        return view('backend.testimonials.create',['testimonials' => $testimonials,'url' => $this->url]);  
    }

     /**
     * Update Testimonials
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
		$postData = $request->all();
        $this->validate($request, [
            'testimonial_name' => 'required',            
            'testimonial_designation' => 'required', 
			'testimonial_message' => 'required',
			'testimonial_profile_photo' => 'mimes:jpeg,jpg,png', 			
        ]);
		
        DB::beginTransaction();
        try{
				$testimonials = Testimonials::find($id);
				
				
				if(!empty($request->testimonial_photo))
				{
					$testimonial_profile_photoname = $this->uploadFile($request->testimonial_photo,'testimonials');
					if(!empty($testimonial_profile_photoname))
					{
						$testimonial_profile_photo = $testimonial_profile_photoname;
						$testimonials->testimonial_photo = $testimonial_profile_photo;
					}
				}
				
				
				$testimonials->testimonial_name = $request->input('testimonial_name');
				$testimonials->testimonial_designation = $request->input('testimonial_designation');
				$testimonials->testimonial_message = $request->input('testimonial_message');
				$testimonials->user_id = Auth::user()->id;
				$testimonials->save();
            
			DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.testimonials_update',['name' => $request->input('testimonial_name')]);
            storeActicityLog(trans('messages.testimonials_update'),$message,Auth::user(),$testimonials);
            return redirect()->route('testimonials.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('testimonials.index'); 
        }
    }

    /**
     * Delete Testimonials
     * @param int $id (Testimonials Id)
     * @return Route
     */
    public function delete($id){ 
        $testimonials = Testimonials::where('id',$id)->first();
        $testimonials->status = 0;
        $testimonials->save();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.testimonials_delete',['name' => $testimonials->testimonial_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$testimonials);
        return redirect()->route('testimonials.index');
    }
}
