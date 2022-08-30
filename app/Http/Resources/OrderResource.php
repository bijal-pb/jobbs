<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        [
        'id' => $this->id,
        'reach_time' => $this->reach_time,
        'start_time' => $this->start_time,
        'complete_time' => $this->complete_time,
        'price' => $this->price,
        'service_fee' => $this->service_fee,
        'discount' => $this->discount,
        'total_amount' => $this->total_amount,
        'payment_status' => $this->payment_status,
        'user_request_id' => $this->user_request_id,  
        ];
    }
}
