<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class NotificationController extends Controller
{
    /**
    *  @OA\Get(
    *     path="/api/notifications",
    *     tags={"Notifications"},
    *     summary="Get Notifications",
    *     security={{"bearer_token":{}}},
    *     operationId="logout",
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
   public function notifications()
   {
       $notification = Notification::where('user_id',Auth::id())->orderBy('id','desc')->get();
       $unread_count = Notification::where('user_id',Auth::id())
                                   ->where('status',1)->count();
       return response()->json([
           'data' =>  $notification,
           'unread' => $unread_count,
       ],200);
   }

   /**
   *  @OA\Get(
   *     path="/api/read-notifications",
   *     tags={"Read Notification"},
   *     summary="Change status of notification.",
   *     security={{"bearer_token":{}}},
   *     operationId="logout",
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
   public function read_notifications()
   {
       $notifications = Notification::where('user_id',Auth::id())
                       ->where('status',1)
                       ->get();
       foreach($notifications as $noti)
       {
           $notification = Notification::find($noti->id);
           $notification->status = 2;
           $notification->save();
       }
       return response()->json([
           'message' => 'Read status set successfully!',
       ],200);
   }

   public function sendMail()
   {
        $subject = 'Request a features';
        $to = array('email'=>'yaxu.ingeniousmindslab@gmail.com','name'=>'Medical App');

        Mail::send(['html' => 'admin/mails/testMail'], ['title' => $subject], function($message) use ( $subject, $to){
            $message->to($to['email'],$to['name'])->subject($subject);
        });
        return response()->json('Mail send successfully',200);
    }

    /**
   *  @OA\Get(
   *     path="/api/test-notification",
   *     tags={"test Notification"},
   *     summary="Test Notification.",
   *     security={{"bearer_token":{}}},
   *     operationId="test",
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
    public function test_notifications()
    {
        $user = User::find(Auth::id());
        $result = sendPushNotification($user->device_token,'Test','Test Notification', 1,$user->id);
        return $result;
    }
}
