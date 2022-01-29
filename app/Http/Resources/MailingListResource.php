<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MailingListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'text' => $this->text,
            'start' => $this->start,
            'status' => $this->status,
            'allow_send_from' => substr($this->allow_send_from, 0, 5),
            'allow_send_to' => substr($this->allow_send_to, 0, 5),
            'segments' => $this->segments->pluck('id'),
            'selected_channels' => $this->selected_channels,
            'email_teplate' => $this->email_teplate,
        ];
    }
}
