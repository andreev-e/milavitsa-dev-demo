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
            'sms' => (bool) $this->sms,
            'email' => (bool) $this->email,
            'telegram' => (bool) $this->telegram,
            'whatsapp' => (bool) $this->whatsapp,
            'start' => $this->start ? $this->start->format('Y.m.d H:i') : null,
            'allow_send_from' => substr($this->allow_send_from, 0, 5),
            'allow_send_to' => substr($this->allow_send_to, 0, 5),
        ];
    }
}
