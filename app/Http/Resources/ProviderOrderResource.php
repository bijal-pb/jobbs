<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProviderOrderResource extends JsonResource
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
            'reference_no' => $this->reference_no,
            'user_request_id' => $this->user_request_id,
            'address' => $this->userRequest->address,
            'lat' => $this->userRequest->lat,
            'lang' => $this->userRequest->lang,
            'start_date' => $this->userRequest->start_date,
            'end_date' => $this->userRequest->end_date,
            'start_time' => $this->userRequest->start_time,
            'end_time' => $this->userRequest->end_time,
            'customer_id' => $this->userRequest->from,
            'customer_name' => $this->userRequest->from_user->first_name. ' '.$this->userRequest->from_user->last_name,
            'customer_rate' => 0,
            'provider_id' => $this->userRequest->to,
            'provider_name' => $this->userRequest->to_user->first_name. ' '.$this->userRequest->to_user->last_name,
            'provider_rate' => 0,
            'service_category' => $this->userRequest->user_service->service_category->name,
            'service_category_id' => $this->userRequest->user_service->service_category_id,
            'service' => $this->userRequest->user_service->service_category->service->name,
            'service_id' => $this->userRequest->user_service->service_category->service_id,
        ];
    }
}
