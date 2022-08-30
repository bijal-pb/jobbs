<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Http\Resources\FeedbackResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Mail;
class FeedbackController extends BaseController
{
   /**
	 *  @OA\Post(
	 *     path="/api/add/feedback",
	 *     tags={"add feedback"},
	 *     summary="add feedback",
	 *     security={{"bearer_token":{}}},
	 *     operationId="add/feedback",
     * 
     *     @OA\Parameter(
	 *         name="rate",
	 *         required=true,
	 *         in="query",
	 *         @OA\Schema(
	 *             type="integer"
	 *         )
	 *     ),
     *     @OA\Parameter(
	 *         name="name",
	 *         in="query",
	 *        required=true,		
	 *         @OA\Schema(
	 *             type="string"
	 *         )
     *     ),
     *      @OA\Parameter(
	 *         name="email",
	 *         in="query",	
	 *         @OA\Schema(
	 *             type="string"
	 *         )
     *     ),
     *        @OA\Parameter(
	 *         name="feedback",
	 *         in="query",		
	 *         @OA\Schema(
	 *             type="text"
	 *         )
     *     ),
     *       @OA\Parameter(
	 *         name="suggestion",
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
    public function add_feedback(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'rate' => 'required',
			'name' => 'required',           
        ]);
		if($validator->fails())
 		{
 			return $this->sendError($validator->errors()->first(),400);
 		}
		try{
			$user_feedback = new Feedback;
            $user_feedback->rate = $request->rate;
            $user_feedback->name = $request->name;
            $user_feedback->email = $request->email;
            $user_feedback->feedback= $request->feedback;
            $user_feedback->suggestion= $request->suggestion;
			$user_feedback->save();
			$data = [
    
                'rate' => $user_feedback->rate,
				'name' => $user_feedback->name,
				'email' =>  $user_feedback->email,
				'feedback' => $user_feedback->feedback,
				'suggestion' => $user_feedback->suggestion,
            ];
            $email = $user_feedback->email;
            Mail::send('mail.feedbackMail', $data, function($message) use ($email) {
                $message->to($email, 'feedback')->subject
                   ('Feedback mail');
            });
			// $success = null;
            // return $this->sendResponse($success, 'Email sent succesfully!');
			//$user_feedback =  new FeedbackResource($user_feedback);
			return $this->sendResponse($user_feedback, 'feedback added successfully!.');         
		}catch(Exception $e)
        {
            return $this->sendError($e->getMessage(),422);
        }
        
    }
 
}
