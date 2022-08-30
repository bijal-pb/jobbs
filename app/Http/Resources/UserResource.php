<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'email' => $this->email,
            'country_code' => $this->country_code,
            'phone' => $this->phone,
            'phone_verified' => $this->phone_verified,
            'decoument_approved' => $this->document_approved,
            'rating' => $this->rating,
            'is_like' => isset($this->is_like) ? $this->is_like : null,
            'address' => $this->address,
            'lat' => $this->lat,
            'lang' => $this->lang,
            'bio' => $this->bio,
            'photo' => $this->photo,
            'device_type' => $this->device_type,
            'device_token' => $this->device_token,
            'firebase_id' => $this->firebase_id,
            'provider' => isset($this->user_setting) ? $this->user_setting->provider : null, 
            'online' => isset($this->user_setting) ? $this->user_setting->online :  null,
            'notification' => isset($this->user_setting) ?  $this->user_setting->notification : null,
            'services' => isset($this->user_service) ? $this->user_service : null,
            'total_jobbs' => isset($this->total_jobbs) ? $this->total_jobbs : null,
            'reviews' => isset($this->reviews) ? $this->reviews : null,
            
        ];
    }
}
