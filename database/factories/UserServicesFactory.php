<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\ServiceCategories;
use App\Models\UserServices;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserServicesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserServices::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'service_category_id' => ServiceCategories::inRandomOrder()->first()->id,
            'price' => $this->faker->numberBetween(5, 200),
            'status' =>$this->faker->randomElement(["1"]),
        
        ];
    }
}
