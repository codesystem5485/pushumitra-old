<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Books;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use Response;
use App\Traits\FileUpload;
use File;
use Config;

class BookController extends Controller
{
    use FileUpload;
    protected $url = '';
   
    /**
     * Book Type Construct 
     * @return url 
     */
    public function __construct(){

        $this->middleware('permission:book-list|book-create|book-edit|book-delete', ['only' => ['index','show']]);
        $this->middleware('permission:book-create', ['only' => ['create','store']]);
        $this->middleware('permission:book-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:book-delete', ['only' => ['delete']]);
        
       $this->url = [   
            'listUrl' => route('book.index'),
            'createUrl' => route('book.create')
        ];
    } 

    /**
     * Book List
     * @return View
     */
    public function index(){
		
		//$res = DB::table('books')->whereRaw("find_in_set('Pashumitra',book_role)")->get();
		//print_r($res);exit;
        $books = Books::orderBy('id','ASC')->get();
        return view('backend.book.index',['books'=>$books,'url' => $this->url]); 
    }

    /**
     * Add Library View
     * @return View
     */
    public function create(){
        $permission = Permission::get();
        return view('backend.book.create',['permission'=>$permission,'url' => $this->url]); 
    }
    /**
     * Store Library
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function store(Request $request){
        $this->validate($request, [
            'book_name' => 'required|unique:books,book_name',            
            'book_file' => 'required|unique:books,book_file|max:52240', 
			//'book_role' => 'required', 			
        ]);
        DB::beginTransaction();
        try{
			
			$file = $request->book_file;
			$book_name = $request->book_name;
			
			$extension = $file->getClientOriginalExtension();
	
            //$fileName = $this->uploadFile($request->book_file,'book');
			
			$path = Config::get('constants.file.book_file_path');
			
			$fileName = $book_name.'.'.$file->extension();
            $file->move(public_path($path), $fileName);
			$str = '';
			if($request->book_role!=''){
				$array = $request->book_role;
				$str = implode(",", $array);
			}
			
            $book = Books::create(['book_name' => $request->input('book_name'),'book_file'=>$fileName,'book_role'=>$str]);
            DB::commit();
            Session::flash('success', trans('messages.create_records'));
            
            ## Store log
            $message = trans('messages.book_create',['name' => $request->input('book_name')]);
            storeActicityLog(trans('messages.book_create'),$message,Auth::user(),$book);
            return redirect()->route('book.index');
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('book.index');   
            
        }
     
    }

    /**
     * Get Particular Books
     * @param int $id (Book Id) Request $request
     * @return View
     */
    public function edit(Request $request, $id = ''){
        $books = Books::find($id);
        return view('backend.book.create',['books' => $books,'url' => $this->url]);  
    }

     /**
     * Update book
     * @param Request $request
     * @thorw exception
     * @return Route
     */
    public function update(Request $request, $id) 
    {
        $this->validate($request, [
            'book_name' => 'required|unique:books,book_name,'.$id,
            'book_file' => 'unique:books,book_file|max:52240',            
        ]);
        DB::beginTransaction();
        try{
            $book = Books::find($id);
            if($request->book_file)
            {
                $this->removeFile($book->book_file,'book');
				
				if($request->book_file!='')
				{
					$file = $request->book_file;
					$book_name = $request->book_name;
					$extension = $file->getClientOriginalExtension();
					$path = Config::get('constants.file.book_file_path');
				
					$fileName = $book_name.'.'.$file->extension();
					$file->move(public_path($path), $fileName);
					 $book->book_file = $fileName;
				}
				
            }
			$str = '';
				if($request->book_role!=''){
					$array = $request->book_role;
					$str = implode(",", $array);
				}
            $book->book_name = $request->input('book_name');
			$book->book_role = $str;
            $book->save();
            DB::commit();
            Session::flash('success', trans('messages.update_records'));

            ## Store log
            $message = trans('messages.book_update',['name' => $request->input('book_name')]);
            storeActicityLog(trans('messages.update'),$message,Auth::user(),$book);
            return redirect()->route('book.index');    
        }catch(\Exception $e){ 
            DB::rollback();
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,Auth::user());
            Session::flash('error', trans('messages.something'));
            return redirect()->route('book.index'); 
        }
    }

    /**
     * Delete book
     * @param int $id (book Id)
     * @return Route
     */
    public function delete($id){ 
        $book = Books::where('id',$id)->first();
        $this->removeFile($book->book_file,'book');

        $book->delete();
        Session::flash('success', trans('messages.delete_records'));
        
        ## Store log
        $message = trans('messages.book_delete',['name' => $book->book_name]);
        storeActicityLog(trans('messages.delete'),$message,Auth::user(),$book);
        return redirect()->route('book.index');
    }

    public function getDownload($file_name){

        $file = public_path()."/upload/book/".urldecode($file_name);
       /* $headers = array('Content-Type: application/pdf',
						'Access-Control-Allow-Origin:*','Access-Control-Allow-Methods:
		GET, POST, PUT, DELETE, OPTIONS');
        return Response :: download($file);
        
       //  return response()->download($file, $file_name, $headers);
        return Response::download($file,$file_name, $headers);*/
        
        $file = public_path()."/upload/book/".urldecode($file_name);
        $headers = array('Content-Type: application/pdf',
						'Access-Control-Allow-Origin:*','Access-Control-Allow-Methods:
		GET, POST, PUT, DELETE, OPTIONS');
        return Response :: download($file);
        
       //  return response()->download($file, $file_name, $headers);
        return Response::download($file,$file_name, $headers);
    }

}
