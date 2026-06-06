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
use App\Models\ReferenceModel;

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
	
	public function index(Request $request){
		
		$to_date = '';$from_date='';$activity=''; $referenceid='';
		if($request->from_date!=''){
			$from_date = date("Y-m-d", strtotime($request->from_date))." 00:00:01";
		}
		if($request->to_date!=''){
			$to_date = date("Y-m-d", strtotime($request->to_date))." 00:00:01";
			 
		}
		if($request->activity!=''){
			$activity = $request->activity;
		}
		
		if($request->referenceid!=''){
			$referenceid = $request->referenceid;
		}
		
		
         $payments = Payments::select(
						'payments.*',
						'fee_structure.name',
						'users.pm_code',
						'users.rv_code',
						'users.other_usercode',
						'users.full_name',
						'users.first_name',
						'users.middle_name',
						'users.last_name',
						'users.mobile_number',
						DB::raw('COALESCE((select `references`.name from user_references left join `references` on `references`.id = user_references.referenceid where user_references.userid = payments.user_id order by user_references.id desc limit 1), (select `references`.name from `references` where `references`.id = 1 limit 1), "Pashumitra advertisement") as reference_name')
					)
					->leftJoin('fee_structure', 'fee_structure.id', '=', 'payments.type')
					->leftJoin('users', 'users.id', '=', 'payments.user_id')
					->where('fee_structure.reg_flag',0);
					
					if($from_date!='' && $to_date!=''){
						$payments = $payments->whereBetween('payment_date', [$from_date, $to_date]);
					}
					if($activity!=''){
						$payments = $payments->where('type',$activity);
					}
					
					if($referenceid!=''){
						if($referenceid == 1){
							$payments = $payments->where(function($query) use ($referenceid) {
								$query->whereIn('payments.user_id', function($subQuery) use ($referenceid) {
									$subQuery->select('userid')
										->from('user_references')
										->where('referenceid', $referenceid);
								})
								->orWhereNotIn('payments.user_id', function($subQuery) {
									$subQuery->select('userid')
										->from('user_references');
								});
							});
						}else{
							$payments = $payments->whereIn('payments.user_id', function($query) use ($referenceid) {
								$query->select('userid')
									->from('user_references')
									->where('referenceid', $referenceid);
							});
						}
					}
					
					$payments = $payments->orderBy('payment_date','DESC')->get();
					$totalBusinessCount = $payments->count();
					$totalBusinessAmount = $payments->sum('amount');
	
			
		DB::connection()->enableQueryLog();
        
		//	dd(DB::getQueryLog());
		$fromdate='';$todate=''; $selactivity=''; $selreferenceid='';
		if($from_date!=''){
			$fromdate = date("d-m-Y",strtotime($from_date));
		}
		if($to_date!=''){
			$todate =date("d-m-Y",strtotime($to_date));
		}
		if($activity!=''){
			$selactivity =$activity; 
		}
		if($referenceid!=''){
			$selreferenceid =$referenceid;
		}
		$fees = Fee::where('fee_structure.reg_flag',0)->orderBy('id','ASC')->get();
		$references = ReferenceModel::where('active',1)->orWhere('id',1)->orderBy('name','ASC')->get();
        foreach($payments as $row){
			$user_code = '';
			if($row->pm_code){
				$user_code = $row->pm_code;
			}elseif($row->rv_code){
				$user_code = $row->rv_code;
			}elseif($row->other_usercode){
				$user_code = $row->other_usercode;
			}elseif($row->mobile_number){
				$user_code = $row->mobile_number;
			}elseif($row->first_name || $row->last_name){
				$user_code = $row->first_name.' '.$row->last_name;
			}elseif($row->full_name){
				$user_code = $row->full_name;
			}else{
				$user_code = 'N/A';
			}
			$row->user_code = $user_code;
		}
		
        return view('backend.payment_reports.index',['from_date'=>$fromdate,'to_date'=>$todate,'selactivity'=>$selactivity,'selreferenceid'=>$selreferenceid,'fees'=>$fees,'references'=>$references,'payments'=>$payments,'totalBusinessCount'=>$totalBusinessCount,'totalBusinessAmount'=>$totalBusinessAmount,'url' => $this->url]);
    }
	
	public function registrationPaymentReport(Request $request){
		$to_date = '';$from_date='';$referenceid='';
		if($request->from_date!=''){
			$from_date = date("Y-m-d", strtotime($request->from_date))." 00:00:01";
		}
		if($request->to_date!=''){
			$to_date = date("Y-m-d", strtotime($request->to_date))." 00:00:01";
			 
		}
		if($request->referenceid!=''){
			$referenceid = $request->referenceid;
		}
        /*$payments = Payments::select('payments.*','fee_structure.name','users.pm_code','users.full_name','users.rv_code')
					->leftJoin('fee_structure', 'fee_structure.id', '=', 'payments.type')
					->leftJoin('users', 'users.id', '=', 'payments.user_id')
					->where('fee_structure.reg_flag',1)
					->orderBy('payment_date','DESC')->get();*/
					
		$payments = Payments::select(
						'payments.*',
						'fee_structure.name',
						'users.pm_code',
						'users.city_town',
						'users.full_name',
						'users.rv_code',
						'users.other_usercode',
						'users.first_name',
						'users.middle_name',
						'users.last_name',
						'users.mobile_number',
						DB::raw('COALESCE((select `references`.name from user_references left join `references` on `references`.id = user_references.referenceid where user_references.userid = payments.user_id order by user_references.id desc limit 1), (select `references`.name from `references` where `references`.id = 1 limit 1), "Pashumitra advertisement") as reference_name')
					)
					->leftJoin('fee_structure', 'fee_structure.id', '=', 'payments.type')
					->leftJoin('users', 'users.id', '=', 'payments.user_id')
					->where('fee_structure.reg_flag',1);
					
					if($from_date!='' && $to_date!=''){
						$payments = $payments->whereBetween('payment_date', [$from_date, $to_date]);
					}
					if($referenceid!=''){
						if($referenceid == 1){
							$payments = $payments->where(function($query) use ($referenceid) {
								$query->whereIn('payments.user_id', function($subQuery) use ($referenceid) {
									$subQuery->select('userid')
										->from('user_references')
										->where('referenceid', $referenceid);
								})
								->orWhereNotIn('payments.user_id', function($subQuery) {
									$subQuery->select('userid')
										->from('user_references');
								});
							});
						}else{
							$payments = $payments->whereIn('payments.user_id', function($query) use ($referenceid) {
								$query->select('userid')
									->from('user_references')
									->where('referenceid', $referenceid);
							});
						}
					}
					
					$payments = $payments->orderBy('payment_date','DESC')->get();
					$totalBusinessCount = $payments->count();
					$totalBusinessAmount = $payments->sum('amount');
					
		$fromdate='';$todate='';$selreferenceid='';
		if($from_date!=''){
			$fromdate = date("d-m-Y",strtotime($from_date));
		}
		if($to_date!=''){
			$todate =date("d-m-Y",strtotime($to_date));
		}
		if($referenceid!=''){
			$selreferenceid =$referenceid;
		}
		$references = ReferenceModel::where('active',1)->orWhere('id',1)->orderBy('name','ASC')->get();
		foreach($payments as $row){
			$user_code = '';
			if($row->pm_code){
				$user_code = $row->pm_code;
			}elseif($row->rv_code){
				$user_code = $row->rv_code;
			}elseif($row->other_usercode){
				$user_code = $row->other_usercode;
			}elseif($row->mobile_number){
				$user_code = $row->mobile_number;
			}elseif($row->first_name || $row->last_name){
				$user_code = $row->first_name.' '.$row->last_name;
			}elseif($row->full_name){
				$user_code = $row->full_name;
			}else{
				$user_code = 'N/A';
			}
			$row->user_code = $user_code;
		}

        return view('backend.payment_reports.reg_payment_report',['from_date'=>$fromdate,'to_date'=>$todate,'selreferenceid'=>$selreferenceid,'references'=>$references,'payments'=>$payments,'totalBusinessCount'=>$totalBusinessCount,'totalBusinessAmount'=>$totalBusinessAmount,'url' => $this->url]); 
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
