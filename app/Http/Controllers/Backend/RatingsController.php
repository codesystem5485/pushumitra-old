<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ratings;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;



class RatingsController extends Controller
{
    
    protected $url = '';
   
    /**
     * Rating Construct 
     * @return url 
     */
    public function __construct(){

        $this->middleware('permission:rating-list|rating-delete',['only' => ['index','show']]);
        $this->middleware('permission:testimonratingial-delete', ['only' => ['delete']]);
        
       $this->url = [   
            'listUrl' => route('ratings.index'),
            
        ];
    } 

    /**
     * Ratings List
     * @return View
     */
    public function index(){
        $ratings = Ratings::leftJoin('users', 'users.id', '=', 'review_ratings.rateable_id')
		->select('review_ratings.*','users.full_name')
		->where('status',1)->orderBy('id','DESC')->get();
        return view('backend.ratings.index',['ratings'=>$ratings,'url' => $this->url]); 
    }
     /**
     * Delete 
     * @param int $id (rating Id)
     * @return Route
     */
    public function delete($id){ 
        $ratings = Ratings::where('id',$id)->first();
        $ratings->delete();
		Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.ratings_delete',['name' => $ratings->testimonial_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$ratings);
        return redirect()->route('ratings.index');
    }
}
