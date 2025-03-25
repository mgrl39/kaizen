<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'birthdate' => $this->birthdate,
            'links' => [
                'self' => route('users.show', $this->id),
                'bookings' => route('users.bookings', $this->id)
            ]
        ];
    }
} 