<?php

namespace Database\Factories;

use App\Models\UserDocument;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
       
        return [
           
                'user_id' => User::inRandomOrder()->first()->id,
                'document_type_id' => DocumentType::inRandomOrder()->first()->id,
                'document' => $this->faker->image('public/images', 640, 480, null, false),
                'status' => 1,
            
        ];
    }
}
