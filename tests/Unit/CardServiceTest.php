<?php

namespace Tests\Unit;

use App\Services\CardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test; // Importar el atributo
use Tests\TestCase;

class CardServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_card_and_associates_it_with_a_user()
    {
        // Mock del request de entrada
        $request = (object) [
            'dni' => '12345678',
            'name' => 'John',
            'lastname' => 'Doe',
            'bank_entity_name' => 'BankCorp',
            'number_card' => '1111-1111',
        ];

        // Instancia del servicio
        $service = new CardService();

        // Ejecutar la función
        $card = $service->addCard($request);

        // Verificar que el usuario fue creado en la base de datos
        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'lastname' => 'Doe',
            'dni' => '12345678',
        ]);

        // Verificar que la tarjeta fue creada en la base de datos
        $this->assertDatabaseHas('cards', [
            'bank_entity_name' => 'BankCorp',
            'number_card' => '1111-1111',
        ]);

        // Verificar la relación entre el usuario y la tarjeta
        $this->assertEquals('12345678', $card->user->dni);
    }

    #[Test]
    public function it_rolls_back_on_error()
    {
        $this->expectException(\Exception::class);

        $request = (object) [
            'dni' => null, // Esto provocará un error
            'name' => 'John',
            'lastname' => 'Doe',
            'bank_entity_name' => 'BankCorp',
            'number_card' => '1111-1111',
        ];

        $service = new CardService();
        $service->addCard($request);

        // Verificar que no hay datos en la base de datos
        $this->assertDatabaseMissing('users', ['name' => 'John']);
        $this->assertDatabaseMissing('cards', ['number_card' => '1111-1111']);
    }
}