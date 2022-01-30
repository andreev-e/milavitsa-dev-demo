<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MailingListCollection extends JsonResource
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
            'selected_channels' => $this->selected_channels,
            'start' => $this->start ? $this->start->format('Y.m.d H:i') : null,
            'allow_send_from' => substr($this->allow_send_from, 0, 5),
            'allow_send_to' => substr($this->allow_send_to, 0, 5),
            'status' => $this->status,
            'segments' => $this->segments->pluck('name', 'id'),
            'chunk' => $this->chunk,
        ];
    }
}
