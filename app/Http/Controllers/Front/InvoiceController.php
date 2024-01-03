<?php

namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use DB;
use Session;
use Auth;
use App\Traits\FileUpload;
use App\Models\Fee;
use Response;
use App\Models\Payments;
use PDF;

class InvoiceController extends Controller
{
    use FileUpload;
    protected $url = '';
    
    public function __construct(){

        
    } 

    /**
     * Home page
     * @return View
     */
    public function downloadReceipt($userId,$paymentId){
		
        $payments = Payments::leftJoin('users', 'users.id', '=', 'payments.user_id')
		->select('payments.*','users.full_name','users.mobile_number','users.address_line_1','users.pincode')
		->where('payments.id',$paymentId)->first();
		if($payments){
		
		$type = Fee::where('id',$payments->type)->first();
		$typeDetails='';
		if($type){
			$typeDetails = $type->name;
		}
		
		$data = [
            'user_name'    => $payments->full_name,
            'mobile_number' => $payments->mobile_number,
            'invoice_number'      => $paymentId,
            'invoice_date' => date("d-m-Y",strtotime($payments->payment_date)),
            'amount'        =>$payments->amount,
			'type'        =>$typeDetails,
        ];
        $pdf = PDF::loadView('invoice', $data);
        return $pdf->stream('invoice.pdf');
		}else{
			
		}
		
       // return view('front.invoices.payment_invoice',compact('payments')); 
    }
	
	public function getDownload($file_id){ 
		
		$books = Books::where('id',$file_id)->first();
		$bookname = $books->book_name.'.pdf';
		$file_name = $books->book_file;
		$file = public_path()."/upload/book/".urldecode($file_name);
        $headers = array('Content-Type: application/pdf','Access-Control-Allow-Origin:*','Access-Control-Allow-Methods:GET, POST, PUT, DELETE, OPTIONS');
        //return Response :: download($file);
        
       //  return response()->download($file, $file_name, $headers);
        return Response::download($file,$bookname, $headers);
    }
	
}
