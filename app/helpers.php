<?php

use App\Models\Notification;
use App\Models\Setting;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use Twilio\TwiML\VoiceResponse;


function sendPushNotification($device_token,$noti_title,$noti_body,$badge,$user_id = null,$request_id = null)
{
    $notification = New Notification; 
    $notification->user_id = $user_id;
    $notification->request_id = $request_id;
    $notification->title = $noti_title;
    $notification->message = $noti_body;
    $notification->save();
    $setting = Setting::latest()->first();

    $url = "https://fcm.googleapis.com/fcm/send";
    $registrationIds = array($device_token);
    $serverKey = $setting->fcm;
    $title = $noti_title;
    $body = $noti_body;
    $data = null;
    if($request_id != null){
        $data = ['request_id' => $request_id];
    }
    $notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' =>$badge);

    $arrayToSend = array('registration_ids' => $registrationIds, 'data' => $data,'notification'=>$notification,'priority'=>'high', "content_available"=> true, "mutable_content"=> true);
    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Send the request
    $result = curl_exec($ch);
    if ($result === FALSE) 
    {
        die('FCM Send Error: ' . curl_error($ch));
    }

    curl_close( $ch );
    return $result;
}

function sendSms($receiverNumber, $message)
{
    try {
  
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_TOKEN");
        $twilio_number = env("TWILIO_FROM");

        $client = new Client($account_sid, $auth_token);
        $client->messages->create($receiverNumber, [
            'from' => $twilio_number, 
            'body' => $message]);

        return true;

    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function voiceCall($request)
{
    try {
  
        $to = +919428557736;
		$from = +19739102081;

        $response = new VoiceResponse();
		$dial = $response->dial('', ['callerId' => $from]);
		$dial->number($to);
		return $response;

    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function twillioVoiceToken($identity)
{
    try {
		$twilioAccountSid = env("TWILIO_SID");
		$twilioApiKey = env("TWILL_API_KEY");
		$twilioApiSecret = env("TWILL_API_SECRET");
		// echo $user_info[0]['name'];
		// Required for Voice grant
		$outgoingApplicationSid = env("TWILL_OUTGOING");
		// $push_credential_sid = TWILL_PUSH_VOIP_APN;
		// An identifier for your app - can be anything you'd like
		// $identity = Auth::id();

		// Create access token, which we will serialize and send to the client
		$token = new AccessToken(
			$twilioAccountSid,
			$twilioApiKey,
			$twilioApiSecret,
			3600,
			$identity
		);

		// Create Voice grant
		$voiceGrant = new VoiceGrant();
		$voiceGrant->setOutgoingApplicationSid($outgoingApplicationSid);
		// $voiceGrant->setPushCredentialSid($push_credential_sid);
		// Optional: add to allow incoming calls
		$voiceGrant->setIncomingAllow(true);

		// Add grant to token
		$token->addGrant($voiceGrant); 

		// render token to string
		$token = $token->toJWT();

        return $token;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}


