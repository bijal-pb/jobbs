<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRequestsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $photo = null;
        if($this->photo != null)
        {
            $photo = url('/uploads/'.$this->photo);
        }
        return [
            'id' => $this->id,
            'provider_id' => $this->to, 
            'from' =>$this->from,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'address' => $this->address,
            'lat' => $this->lat,
            'lang' => $this->lang,
            'user_service_id' => $this->user_service_id, 
            'provider_id' =>$this->to,
            'provider_name' =>$this->to_user->first_name.' '.$this->to_user->last_name,
            'image' => $this->to_user->photo,
            'bio' =>$this->to_user->bio,
            'address' => $this->address,
            'status' => $this->status,
            'service_charge' => $this->service_charge,
            'discount' => $this->discount,
            'sub_total' => $this->sub_total,
            'user_service_id' => $this->user_service_id,
            'user_rate'=> 0,
            'user_service_detail' =>$this->userservice,
            'from_user' =>$this->from_user,
            'to_user' =>$this->to_user,
            'order_review' =>$this->order_review,
            'order_status' =>$this->order_status
            // 'service_detail' =>$this->servicecategories

        ];
    }
}
