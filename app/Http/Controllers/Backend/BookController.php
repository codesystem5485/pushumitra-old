<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Books;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;


class BookController extends Controller
{
    use FileUpload;
    protected $url = '';
   
    /**
     * Book Type Construct 
     * @return url 
     */
    public function __construct(){

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
            'book_file' => 'required|unique:books,book_file|max:10240',            
        ]);
        DB::beginTransaction();
        try{
            $fileName = $this->uploadFile($request->book_file,'book');
            $book = Books::create(['book_name' => $request->input('book_name'),'book_file'=>$fileName]);
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
            'book_file' => 'unique:books,book_file|max:10240',            
        ]);
        DB::beginTransaction();
        try{
            $book = Books::find($id);
            if($request->book_file)
            {
                $this->removeFile($book->book_file,'book');
                $sFileName = $this->uploadFile($request->book_file,'book');
                if(!empty($sFileName)) {
                    $book->book_file = $sFileName;
                }
            }
            $book->book_name = $request->input('book_name');
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

}
