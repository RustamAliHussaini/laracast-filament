<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Speaker;
use App\Models\Talk;

class SpeakerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Speaker::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $qaulificationsCount = $this->faker->numberBetween(0 , 10);
        $qualifications = $this->faker->randomElements(array_keys(Speaker::Qualifications));
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'bio' => $this->faker->text(),
            'twitter_handle' => $this->faker->word(),
            'qualifications' =>$qualifications,
        ];
    }

    public function withTalks(int $count = 1 ): self
    {
        return $this->has(Talk::factory()->count($count) , 'talks');
    }
}
