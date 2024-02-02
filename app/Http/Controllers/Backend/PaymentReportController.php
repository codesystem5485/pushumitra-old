<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Fee;
use App\Models\Payments;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\AddanimalProcessRequest;
use App\Repositories\Interfaces\User\UserRepositoryInterface;
use App\Repositories\Interfaces\User\UserDetailRepositoryInterface;
use DB;
use Session;
use Auth;

class PaymentReportController extends Controller
{
    private $userRepo;
    private $userDetailRepo;
	
    public function __construct(
        UserRepositoryInterface $userRepository,
        UserDetailRepositoryInterface $userDetailRepository ){
			 /* $this->middleware('permission:fees-list|fees-create|fees-edit|fees-delete', ['only' => ['index','show']]);
        $this->middleware('permission:fees-create', ['only' => ['create','store']]);
        $this->middleware('permission:fees-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:fees-delete', ['only' => ['delete']]);*/
        
       $this->url = [   
            'listUrl' => route('paymentreport.index'),
			'createUrl' =>''
        ];
		
		$this->userRepo = $userRepository;
        $this->userDetailRepo = $userDetailRepository;
		
    }
	
	public function index(){
        $payments = Payments::select('payments.*','fee_structure.name','users.pm_code','users.rv_code')
					->leftJoin('fee_structure', 'fee_structure.id', '=', 'payments.type')
					->leftJoin('users', 'users.id', '=', 'payments.user_id')
					->where('fee_structure.reg_flag',0)
					->orderBy('id','DESC')->get();
        return view('backend.payment_reports.index',['payments'=>$payments,'url' => $this->url]); 
    }
	
	public function registrationPaymentReport(){
        $payments = Payments::select('payments.*','fee_structure.name','users.pm_code','users.full_name','users.rv_code')
					->leftJoin('fee_structure', 'fee_structure.id', '=', 'payments.type')
					->leftJoin('users', 'users.id', '=', 'payments.user_id')
					->where('fee_structure.reg_flag',1)
					->orderBy('id','DESC')->get();
        return view('backend.payment_reports.reg_payment_report',['payments'=>$payments,'url' => $this->url]); 
    }
	
	public function addPayments(Request $request)
	{
		$postData = request()->all();
		
		$validator = Validator::make($postData, [
				'role' => 'required',
				'amount' => 'required',
				'type' => 'required',
				'order_id' => 'required',
			]);
			
		if ($validator->fails())
		{
			return $this->sendError([],implode(',',$validator->errors()->all()),400);
		}
		
        $response = [];
		
        DB::beginTransaction();
        try{            
            $aInsertData = $request->all();
		$roleId = null;
		if($aInsertData['role']=="Pashumitra"){
			$roleId = 8;
		}elseif($aInsertData['role']=="Registered-vet")
		{
			$roleId = 7;
		}
		elseif($aInsertData['role']=="Animal-owner")
		{
			$roleId = 6;
		}
		
		$payment_response = $aInsertData['payment_response'];
		$amount = $aInsertData['amount'];
		$type = $aInsertData['type'];
		$order_id =$aInsertData['order_id'];
		
		$paymentId =0;
		$status=0;
		$payment_request = '';
		
		if($aInsertData['payment_id']!=''){
			$paymentId =$aInsertData['payment_id'];
			$status = 1;
		}
		$jsonArr = '';
			if(isset($aInsertData['name']) && isset($aInsertData['mobile_number'])){
				$moduleDetails = array('name'=>$aInsertData['name'],'mobile_number'=>$aInsertData['mobile_number']);
				$jsonArr = json_encode($moduleDetails);
			}
		
		
			$insertArray = array(
					'role_id'=>$roleId,
					'user_id'=>$aInsertData['user_id'],
					'payment_id' =>$paymentId,
					'order_id' =>$aInsertData['order_id'],
					'status' =>$status,
					'payment_date' =>date("Y-m-d H:i:s"),
					'payment_response' =>$payment_response,
					//'payment_request' =>$payment_request,
					'amount' =>$amount,
					'type' =>$type,
					'module_details'=>$jsonArr 
				);
			
			$payment = Payments::create($insertArray);
			
			//pashumitra sign up
			if($roleId==8 && $type==1){
				if($payment)
				{
					//get subscriptions date
					$paymentArr = array( 'type'=>$payment->type,'id'=>$aInsertData['user_id']);
					$subscriptionArr = $this->userRepo->getSubscriptionDates($paymentArr);
					//generate pm_code & update to user table
					$param['pm_code'] = $this->userRepo->generatePashumitraCode();
					$param['subscriptionStartDate']=$subscriptionArr['subscriptionStartDate'];
					$param['subscriptionEndDate']=$subscriptionArr['subscriptionEndDate'];
					$this->userRepo->update($aInsertData['user_id'],$param);  
				}
			}
			
			//registered vet sign up
			if($roleId==7 && $type==6){
				if($payment)
				{ 
					//get subscriptions date
					$paymentArr = array( 'type'=>$payment->type,'id'=>$aInsertData['user_id']);
					$subscriptionArr = $this->userRepo->getSubscriptionDates($paymentArr);
					//generate rv_code & update to user table
					$param['rv_code'] = $this->userRepo->generateRegisteredvetCode();
					$param['subscriptionStartDate']=$subscriptionArr['subscriptionStartDate'];
					$param['subscriptionEndDate']=$subscriptionArr['subscriptionEndDate'];
					$this->userRepo->update($aInsertData['user_id'],$param);  
				}
			}
			
			$response['payments'] =$payment; 
            DB::commit();
			 ## Store log
            $message = trans('messages.payments_create',['name' => $paymentId]);
            storeActicityLog(trans('messages.payments_create'),$message);
			
			return $this->sendResponse($response,trans('messages.payments_create'),200);
        }catch(\Exception $e){
            DB::rollback(); 
            $error = !empty($e->getMessage())?$e->getMessage() : '';
            ##store error log
            storeActicityLog(trans('messages.error'),$error,$request->user_id);
			return  $this->sendError($response,trans('messages.something'),500);			
        }
	}
}
