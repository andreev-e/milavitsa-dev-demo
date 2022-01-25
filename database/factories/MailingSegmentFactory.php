<?php

namespace Database\Factories;

use App\Models\MailingSegment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MailingSegmentFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Сегмент ' . $this->faker->numberBetween($min = 111, $max = 999),
        ];
    }
}
