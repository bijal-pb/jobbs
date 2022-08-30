<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Http\Resources\TransactionResource;
use Exception;
use Auth;

class TransactionController extends BaseController
{
    /**
	 *  @OA\Post(
	 *     path="/api/add/payment",
	 *     tags={"Add payment"},
	 *     summary="add payment",
	 *     security={{"bearer_token":{}}},
	 *     operationId="add/payment",
	 * 
     *     @OA\Parameter(
	 *         name="transaction_id",
	 *         in="query",
     *         description="transaction_id",	
	 *          required=true,
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
	 *     @OA\Parameter(
	 *         name="order_id",
	 *         in="query",
     *         description="order_id",	
	 *          required=true,
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),
     *   	   @OA\Parameter(
	 *         name="user_request_id",
	 *         in="query",
	 * 	 	   description="request_id",				
	 *         required=true,
	 *         @OA\Schema(
	 *            type="integer"
	 *         )
	 *     ),
	 *       @OA\Parameter(
	 *         name="amount",
	 *         in="query",			
	 *         @OA\Schema(
	 *             type="text"
	 *         )
     *     ),
     *     @OA\Parameter(
	 *         name="payment_type",
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
    public function add_payment(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'order_id' => 'required',
            'user_request_id' => 'required',     
		]);
        if($validator->fails()){
			    return $this->sendError($validator->messages()->first(),null,200);
        } 
		try{
			$user_transaction= new Transaction;
			$user_transaction->transaction_id = $request->transaction_id;
			$user_transaction->user_id=  Auth::id();
            $user_transaction->order_id = $request->order_id;
			$user_transaction->user_request_id= $request->user_request_id;
			$user_transaction->amount = $request->amount;
            $user_transaction->payment_type = $request->payment_type;
			$user_transaction->save();
			$user_transaction =  new TransactionResource($user_transaction);
            if($user_transaction->save())  
            {
                $user_order = Order::find($request->order_id);
                $user_order->payment_status = 1;
				$user_order->status = 2;
                $user_order->save();
            }     
			return $this->sendResponse($user_transaction, 'Payment added successfully!.');  
           
		}catch(Exception $e)
        {
            return $this->sendError($e->getMessage(),200);
        }
        
    }
}
