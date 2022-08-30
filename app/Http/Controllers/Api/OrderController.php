<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserRequests;
use App\Http\Resources\UserRequestsResource;
use App\Models\UserServices;
use App\Models\Services;
use App\Http\Resources\UserServicesResource;
use App\Http\Resources\UserOrderRateResource;
use App\Http\Resources\CustomerOrderResource;
use App\Models\UserOrderRate;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProviderOrderResource;
use Exception;
use Auth;
use DB;

class OrderController extends BaseController
{
       /**
	 *  @OA\Post(
	 *     path="/api/customer/request/accept",
	 *     tags={"accept-decline-user-requests"},
	 *     summary="accept decline user requests",
	 *     security={{"bearer_token":{}}},
	 *     operationId="accept-decline-user-requests",
	 * 
	 * 	   @OA\Parameter(
	 *         name="user_request_id",
	 *         in="query",
	 * 	 	   description="request_id",				
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),     
     *     @OA\Parameter(
	 *         name="type",
	 *         in="query",	
     *         description=" 1-accept | 2-decline",		
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
     *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=422,
	 *          description="Unprocessable Entity"
	 *     ),
	 * )
	**/
    public function accept_decline_user_requests(Request $request)
    {
        $validator = Validator::make($request->all(),[
			'user_request_id' => 'required',
			'type' => 'required|in:1,2',
        ]);
        if($validator->fails())
		{
			return $this->sendError($validator->messages()->first(),null,200);
        } 
        try{ 
            if($request->type==1)
            { 
                 $user_req = UserRequests::find($request->user_request_id);

				 $start_date=$user_req->start_date;
				 $end_date=$user_req->end_date;
				 $datetime1 = strtotime($start_date); // convert to timestamps
				 $datetime2 = strtotime($end_date); // convert to timestamps
				 
			     $days = (int)(($datetime2 - $datetime1)/86400);
				//  return $this->sendResponse($days,'');
			
                 $user_req->status=1;
			    if($user_req->save())
				{
				  $order = new Order;
				  $order->reference_no = time().rand(10000,99999);
                  $order->user_request_id=$request->user_request_id; 
				  if($user_req->user_service_id)
				 	{
					      $user_ser = UserServices::find($user_req->user_service_id);
                      	  $order->status=1; 
						  $order->price=$user_ser->price;
						  $order->service_fee = $user_req->service_charge;
						  $order->discount = $user_req->discount;
						  $order->total_amount = $user_req->sub_total;
						  $order->save();
						  $provider = User::find($user_req->to);
						  sendPushNotification($provider->device_token,'Order accepted!','You have accepted an order. '.$order->reference_no.'.',1,$provider->id,$request->user_request_id);  
						  $customer = User::find($user_req->from);
						  sendPushNotification($customer->device_token,'Order accepted!','Your order is accepted by service provider. '.$order->reference_no.'.',1,$customer->id,$request->user_request_id);  
                         return $this->sendResponse($order, 'User request is accpeted');
					}
				
			    }
            }
            else{
                $user_req = UserRequests::find($request->user_request_id);
                $user_req->status=2;
				$user_req->save();
				$provider = User::find($user_req->to);
				sendPushNotification($provider->device_token,'Order rejected!','You have rejected an order. ',1,$provider->id,$request->user_request_id);  
				$customer = User::find($user_req->from);
				sendPushNotification($customer->device_token,'Order rejected!','Your order is rejected by service provider. ',1,$customer->id,$request->user_request_id);  
                return $this->sendResponse($user_req, 'User requests is declined');
                }

        }catch(Exception $e)
        {
			return $this->sendError('Something went wrong, Please try again!.',$e,422);
        }   

    }
	/**
	 *  @OA\Post(
	 *     path="/api/order/work/complete",
	 *     tags={"order work complete"},
	 *     summary="order work complete",
	 *     security={{"bearer_token":{}}},
	 *     operationId="order/work/complete",
     *
     *     @OA\Parameter(
	 *         name="user_request_id",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/
    public function order_work_complete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'user_request_id' => 'required',
		
        ]);
		if($validator->fails())
		{
		return $this->sendError($validator->errors()->first(),400);
		}
		try{
			if($request->user_request_id != null)
			{
				    $order = Order::where('user_request_id',$request->user_request_id)->first();
				    $order->complete_time = carbon::now()->format('Y-m-d h:i:s');
					$order->status = 2;
					$order->save();
					$login_user = User::find(Auth::id());
					$user_request = UserRequests::find($order->user_request_id);
					$user = User::where('id',$user_request->from)->first();
					sendPushNotification($user->device_token,'Order Completed','Your order '.$order->reference_no.' has been completed.','Complete Order',1,$user->id,$order->user_request_id);
					return $this->sendResponse($order, 'Status updated successfully');	
			}
		       
		}catch(Exception $e)
		{
			return $this->sendError('Something went wrong, Please try again!',422);
		}
    }

	/**
     * @OA\Get(
     *     path="/api/provider/weekly/report",
     *     tags={"Provider weekly report (Home)"},
     *     summary="Provider weekly report",
     *     security={{"bearer_token":{}}},
     *     operationId="Provider weekly report",
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     * )
    **/
	public function get_report(Request $request)
	{
		try{
			$date = Carbon::now()->subDays(7);
			$user_requests = UserRequests::where('to',Auth::id())->where('status',1)->pluck('id');
			$orders = Order::whereIn('user_request_id',$user_requests)
							->where('status',2)
							->where('complete_time','>=',$date)
							->get();
			
			$total_earning = 0;
			foreach($orders as $o){
				$total_earning += $o->total_amount;
			}
			$data = [
				'weekly_order' => count($orders),
				'weekly_earning' => $total_earning,
			];
			return $this->sendResponse($data,'Weekly Data');
	    }catch(Exception $e){
        	return $this->sendError($e->getMessage(), 422);
        }
	}
	/**
     * @OA\Get(
     *     path="/api/provider/orders",
     *     tags={"get provider orders"},
     *     summary="get provider orders",
     *     security={{"bearer_token":{}}},
     *     operationId="get provider orders",
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     * )
    **/
	public function get_orders(Request $request)
	{
		try{
			$user_requests = UserRequests::where('to',Auth::id())->where('status',1)->pluck('id');
			$active_orders = Order::with(['userRequest','order_status'])
							->whereIn('user_request_id',$user_requests)
							->whereIn('status',[1,4])
							->orderBy('updated_at','desc')
							->get();
			$completed_orders = Order::with(['userRequest','order_status'])
							->whereIn('user_request_id',$user_requests)
							->where('status',2)
							->orderBy('updated_at','desc')
							->get();
			$cancelled_orders = Order::with(['userRequest','order_status'])
							->whereIn('user_request_id',$user_requests)
							->where('status',3)
							->orderBy('updated_at','desc')
							->get();
			$data = [
				'active' => $active_orders,
				'completed' => $completed_orders,
				'cancelled' => $cancelled_orders,
			];
			return $this->sendResponse($data,'Orders');
		}catch(Exception $e){
        	return $this->sendError($e->getMessage(), 422);
        }
	}
/**
	 *  @OA\Post(
	 *     path="/api/add/order/rate",
	 *     tags={"Add user order rate"},
	 *     summary="add user order rate",
	 *     security={{"bearer_token":{}}},
	 *     operationId="add/user/order/rate",
	 * 
     *     @OA\Parameter(
	 *         name="order_id",
	 *         in="query",
	 *          required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *         @OA\Parameter(
	 *         name="rate_to",
	 *         in="query",			
	 *         @OA\Schema(
	 *             type="text"
	 *         )
   *     ),
	 *       @OA\Parameter(
	 *         name="rate",
	 *         in="query",			
	 *         @OA\Schema(
	 *             type="text"
	 *         )
     *     ),
     *     @OA\Parameter(
	 *         name="review",
	 *         in="query",			
	 *         @OA\Schema(
	 *             type="text"
	 *         )
     *     ),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/
    public function add_user_order_rate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'order_id' => 'required',
			   ]);
        if($validator->fails()){
			    return $this->sendError($validator->messages()->first(),null,200);
        } 

		try{
			$order_rate = new UserOrderRate;
			$order_rate->order_id = $request->order_id;
			$order_rate->rate_by=  Auth::id();
			$order_rate->rate_to= $request->rate_to;
			$order_rate->rate = $request->rate;
            $order_rate->review = $request->review;
			$order_rate->save();
			$order_rate =  new UserOrderRateResource($order_rate);
			return $this->sendResponse($order_rate, 'User order rate added successfully!.');         
		}catch(Exception $e)
        {
            return $this->sendError($e->getMessage(),200);
        }
        
    }
		/**
     *  @OA\Get(
     *     path="/api/get/order",
     *     tags={"get order detail"},
     *     summary="get order detail list",
     *     security={{"bearer_token":{}}},
     *     operationId="get order detail",
	 * 
     *    @OA\Parameter(
     *         name="user_request_id",
     *         in="query",
     *         required=true,
     * 		   description="user request id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     **/
    public function get_order_detail(Request $request)
    {
		$validator = Validator::make($request->all(),[
            'user_request_id' => 'required',     
	     ]);
         if($validator->fails()){
			return $this->sendError($validator->messages()->first(),null,200);
			} 
		try{
			$order = Order::with(['userRequest','order_status','order_review'])->where('user_request_id',$request->user_request_id)->get();
			return $this->sendResponse($order,'order detail.');
		}catch(Exception $e)
		{
			return $this->sendError('Something went wrong, Please try again!.',$e,200);
		}   
	}
	 /**
	 *  @OA\Post(
	 *     path="/api/confirm/Request/service/customer",
	 *     tags={"confirm Request service customer"},
	 *     summary="confirm Request service customer",
	 *     security={{"bearer_token":{}}},
	 *     operationId="confirm/Request/service/customer",
     *
     *     @OA\Parameter(
	 *         name="provider_id",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *     @OA\Parameter(
	 *         name="user_service_id",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *      @OA\Parameter(
	 *         name="start_date",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *        @OA\Parameter(
	 *         name="end_date",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *         @OA\Parameter(
	 *         name="start_time",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *        @OA\Parameter(
	 *         name="end_time",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *         @OA\Parameter(
	 *         name="subtotal",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *       @OA\Parameter(
	 *         name="service_charge",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *       @OA\Parameter(
	 *         name="discount",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *       @OA\Parameter(
	 *         name="address",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *       @OA\Parameter(
	 *         name="lat",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *       @OA\Parameter(
	 *         name="lang",
	 *         in="query",
	 *         required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *     @OA\Response(
    *         response=200,
    *         description="Success",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Invalid request"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="not found"
    *     ),
    * )
    **/
   
   	public function confirm_Request_service_customer(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'provider_id' => 'required',
			'user_service_id' => 'required',
        ]);
        if($validator->fails())
		{
			return $this->sendError($validator->messages()->first(),null,200);
        } 
		try{
			$user_reqs = new UserRequests;
			$user_reqs->to = $request->provider_id;
		    $user_reqs->from = Auth::id();
		    $user_reqs->user_service_id = $request->user_service_id;
			$user_reqs->start_date = $request->start_date;
			$user_reqs->end_date = $request->end_date;
			$user_reqs->start_time = $request->start_time;
			$user_reqs->end_time = $request->end_time;
			$user_reqs->sub_total = $request->subtotal;
			$user_reqs->service_charge = $request->service_charge;
			$user_reqs->discount = $request->discount;
			$user_reqs->address = $request->address;
			$user_reqs->lat = $request->lat;
			$user_reqs->lang = $request->lang;
			$user_reqs->save();
			$provider = User::find($user_reqs->to);
			sendPushNotification($provider->device_token,'Order Request','Your have a new order request.',1,$provider->id,$user_reqs->id);
			$customer = User::find($user_reqs->from);
			sendPushNotification($customer->device_token,'Order Placed!','Your order is placed successfully!',1,$customer->id,$user_reqs->id);
			$data =  new UserRequestsResource($user_reqs);
            return $this->sendResponse($data, 'User requested data is added successfully.');
        }catch(Exception $e){
            return $this->sendError($e->getMessage(),422);
        }
    }
	/**
  
    * @OA\Get(
    *     path="/api/fetch/orders/customer",
    *     tags={"fetch orders customer"},
    *     summary="fetch orders customer",
    *     security={{"bearer_token":{}}},
    *     operationId="fetch orders customer",
    *    
	* 	      @OA\Response(
	*         response=200,
	*         description="Success",
	*         @OA\MediaType(
	*             mediaType="application/json",
	*         )
	*     ),
	*     @OA\Response(
	*         response=401,
	*         description="Unauthorized"
	*     ),
	*     @OA\Response(
	*         response=400,
	*         description="Invalid request"
	*     ),
	*     @OA\Response(
	*         response=404,
	*         description="not found"
	*     ),
	* )
    **/
    public function fetch_orders_customer(Request $request)
    {
      try{
        $user_requests = UserRequests::where('from',Auth::id())->where('status',1)->pluck('id');
        $active_orders = Order::with(['userRequest','order_status'])
                ->whereIn('user_request_id',$user_requests)
				->whereIn('status',[1,4])
				->orderBy('updated_at','desc')
                ->get();
        $completed_orders = Order::with(['userRequest','order_status'])
                ->whereIn('user_request_id',$user_requests)
				->where('status',2)
				->orderBy('updated_at','desc')
                ->get();
        $cancelled_orders = Order::with(['userRequest','order_status'])
                ->whereIn('user_request_id',$user_requests)
				->where('status',3)
				->orderBy('updated_at','desc')
                ->get();
        $data = [
          'active' => $active_orders,
          'completed' => $completed_orders,
          'cancelled' => $cancelled_orders,
        ];
        return $this->sendResponse($data,'Customer Orders');
      }catch(Exception $e){
            return $this->sendError($e->getMessage(), 422);
          }
    }

	/**
    *  @OA\Post(
    *     path="/api/cancel/order",
    *     tags={"cancel order"},
	*     summary="cancel order",
	*     security={{"bearer_token":{}}},
    *     operationId="cancel/order",
    *
    *     
    *     @OA\Parameter(
    *         name="id",
    *         in="query",
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),     
    *     @OA\Response(
    *         response=200,
    *         description="Success",
    *         @OA\MediaType(
    *             mediaType="application/json",
    *         )
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Invalid request"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="not found"
    *     ),
    * )
    **/
    public function cancel_order(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required'
        ]);
		if($validator->fails())
		{
		return $this->sendError($validator->errors()->first(),400);
		}
		try{
			$user_order = Order::find($request->id);
			$user_order->status = 3;
			$user_order->save();
			
			$login_user = User::find(Auth::id());
			$user_request = UserRequests::find($user_order->user_request_id);
			$provider = User::find($user_request->to);
			sendPushNotification($provider->device_token,'Order Cancelled','Your order '.$user_order->reference_no.' is cancelled by service seeker.','Cancelled Order',1,$provider->id,$user_order->user_request_id);
			$login_user = User::find(Auth::id());
			$user_request = UserRequests::find($user_order->user_request_id);
			$customer = User::find($user_request->from);
			sendPushNotification($customer->device_token,'Order Cancelled','oops!!! Your order '.$user_order->reference_no.' has been cancelled.','Cancelled Order',1,$customer->id,$user_order->user_request_id);
			return $this->sendResponse('', 'Order cancelled successfully!.');
		}catch(Exception $e){
			return $this->sendError($e->getMessage(), 422);
        }
    }
	/**
	 *  @OA\Post(
	 *     path="/api/accept/work",
	 *     tags={"accept work"},
	 *     summary="accept work",
	 *     security={{"bearer_token":{}}},
	 *     operationId="accept/work",
	 * 
     *     @OA\Parameter(
	 *         name="id",
	 *         description="order id",
	 *         in="query",
	 *          required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 * 		),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/

    public function accept_work(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);
        if($validator->fails())
        {
          return $this->sendError($validator->errors()->first(),400);
        }
        try{   
				$order = Order::find($request->id);
				$order->status = 2;
				$order->save();	 
				$login_user = User::find(Auth::id());
				$user_request = UserRequests::find($order->user_request_id);
				$user = User::where('id',$user_request->from)->first();
				sendPushNotification($user->device_token,'Order Accepted','Your order '.$order->reference_no.' has been accepted.','Accept Order',1,$user->id,$order->user_request_id);
              	return $this->sendResponse($order,'Order accepted succesfully!.');
          	}catch(Exception $e){
                return $this->sendError($e->getMessage(),422);
        }
    }
	/**
     *  @OA\Get(
     *     path="/api/earning",
     *     tags={"Earning"},
     *     summary="Earning",
     *     security={{"bearer_token":{}}},
     *     operationId="Earning",
	 * 
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     **/
	public function get_earning()
	{
		/********** Weekly ********************/
		$date = Carbon::now()->subDays(7);
		$user_requests = UserRequests::where('to',Auth::id())->where('status',1)->pluck('id');
		$weekly_completed = Order::select(DB::raw("sum(total_amount) as weekly_earning, count(id) as completed"))
						->whereIn('user_request_id',$user_requests)
						->where('status',2)
						->where('complete_time','>=',$date)
						->get();
		$active = Order::whereIn('user_request_id',$user_requests)
						->where('status',1)
						->count();
		$orders = Order::select('orders.*','from.first_name as customer_first_name','from.last_name as customer_last_name','to.first_name as provider_first_name','to.last_name as provider_last_name')
						->leftJoin('user_requests','orders.user_request_id','user_requests.id')
	   					->leftJoin('users as from','user_requests.from','from.id')
						->leftJoin('users as to','user_requests.to','to.id')
						->where('orders.status',2)
					    ->whereIn('user_request_id',$user_requests)
						->where('complete_time','>=',$date)
						->get();
		$data['weekly']['overall'] = [
										'orders' => $weekly_completed[0]->completed + $active,
										'ongoing' => $active,
										'completed' => $weekly_completed[0]->completed,
										'weekly_earning' => $weekly_completed[0]->weekly_earning,
									];
		$data['weekly']['earning'] = $orders;

		/************* monthly ********************/
		
		$month_orders = Order::select('orders.*','from.first_name as customer_first_name','from.last_name as customer_last_name','to.first_name as provider_first_name','to.last_name as provider_last_name')
						 	 ->leftJoin('user_requests','orders.user_request_id','user_requests.id')
	   						 ->leftJoin('users as from','user_requests.from','from.id')
							 ->leftJoin('users as to','user_requests.to','to.id')
	  						 ->whereIn('user_request_id',$user_requests)
							 ->where('orders.status',2)
							->whereMonth('complete_time', Carbon::now()->month)
							->get();
		
		$data['monthly']['earning'] = $month_orders;
		$data['monthly']['chart'] = [];
		
		return $this->sendResponse($data,'order detail.');
	}

	/**
	 *  @OA\Post(
	 *     path="/api/order/start/provider",
	 *     tags={"order start time provider"},
	 *     summary="order start time provider",
	 *     security={{"bearer_token":{}}},
	 *     operationId="oder/start/provider",
	 * 
     *     @OA\Parameter(
	 *         name="order_id",
	 *         description="order id",
	 *         in="query",
	 *          required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 * 		),
	 *     @OA\Response(
	 *         response=200,
	 *         description="Success",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *         )
	 *     ),
	 *     @OA\Response(
	 *         response=401,
	 *         description="Unauthorized"
	 *     ),
	 *     @OA\Response(
	 *         response=400,
	 *         description="Invalid request"
	 *     ),
	 *     @OA\Response(
	 *         response=404,
	 *         description="not found"
	 *     ),
	 * )
	**/

    public function order_start_time(Request $request)
    {
		$validator = Validator::make($request->all(),[
            'order_id' => 'required',
			   ]);
      
		if($validator->fails())
        {
          return $this->sendError($validator->errors()->first(),400);
        }
        try{   
				$order = Order::find($request->order_id);
				$order->reach_time = carbon::now()->format('Y-m-d h:i:s');
				$order->start_time = carbon::now()->format('Y-m-d h:i:s');
				$order->status = 4;
				$login_user = User::find(Auth::id());
				$user_request = UserRequests::find($order->user_request_id);
				$user = User::where('id',$user_request->from)->first();
				sendPushNotification($user->device_token,'Order Active','Your order '.$order->reference_no.' has been active.','Order Activeted',1,$user->id,$order->user_request_id);
				$order->save();	 
              	return $this->sendResponse($order,'Order Start time updated successfully.');
          	}catch(Exception $e){
                return $this->sendError($e->getMessage(),422);
        }
	
	}

}
