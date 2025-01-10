<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition()
    {
        return [
            'number_card' => $this->faker->creditCardNumber,  // Genera un número de tarjeta falso
            'bank_entity_name' => fake()->name(),
            'user_id' => User::factory(), // Relaciona la tarjeta con un usuario creado por su fábrica
        ];
    }

}
