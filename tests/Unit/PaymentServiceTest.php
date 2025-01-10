<?php

namespace Tests\Unit;

use App\Services\PaymentService;
use App\Models\{Card, User};
use App\Models\Payment;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test; // Importar el atributo
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function its_do_payment_valid_request()
    {

        // Crear un usuario para asociarlo con la tarjeta
        $user = User::factory()->create();

        // Crear una tarjeta de ejemplo en la base de datos
        $card = Card::factory()->create([
            'bank_entity_name' => 'BankCorp',
            'number_card' => '1111-1111',
            'user_id' => 1,
        ]);

        // Mock de la validación de request
        $request = [
            'number_card' => '1111-1111',
            'amount' => 1000,
            'quotas' => 3,
        ];

        // Mock del servicio de pago (para evitar llamadas reales)
        $paymentServiceMock = Mockery::mock(PaymentService::class);
        $paymentServiceMock->shouldReceive('calculateAmount')
            ->once()
            ->andReturn(1030); // Monto con recargo para 3 cuotas

        $paymentServiceMock->shouldReceive('checkAmountAvailable')
            ->once()
            ->andReturn(true); // Simular que el límite es suficiente

        $paymentServiceMock->shouldReceive('doPayment')
            ->once();

        // Registrar el servicio mockeado
        $this->app->instance(PaymentService::class, $paymentServiceMock);

        // Hacer la petición al controlador (en este caso la ruta para el método doPayment)
        $response = $this->postJson('/api/v1/posnet/payment', $request);

        // Verificar que la respuesta tenga un código 201 (éxito)
        $response->assertStatus(201);

        // Verificar que el mensaje de éxito se encuentra en la respuesta
        $response->assertJson([
            'Message' => 'Payment Success',
        ]);

    }

    #[Test]
    public function its_do_payment_invalid_amount()
    {

        $user = User::factory()->create();

        // Crear una tarjeta de ejemplo en la base de datos
        $card = Card::factory()->create([
            'number_card' => '1111-1111',
            'user_id' => 1,
        ]);

        // Datos de entrada con monto negativo
        $request = [
            'number_card' => '1111-1111',
            'amount' => -500, // Monto inválido
            'quotas' => 3,
        ];

        // Hacer la petición al controlador
        $response = $this->postJson('/api/v1/posnet/payment', $request);

        // Verificar que el código de estado sea 500 (error)
        $response->assertStatus(500);

        // Verificar que el mensaje de error esté presente
        $response->assertJson([
            'Amount is zero.',
        ]);
    }

    // #[Test]
    // public function its_do_payment_insufficient_limit()
    // {

    //     $user = User::factory()->create();

    //     // Crear una tarjeta de ejemplo en la base de datos
    //     $card = Card::factory()->create([
    //         'bank_entity_name' => 'BankCorp',
    //         'number_card' => '1111-1111',
    //         'user_id' => 1,
    //     ]);

    //     // Datos de entrada con monto suficiente, pero límite insuficiente
    //     $request = [
    //         'number_card' => '1111-1111',
    //         'amount' => 1000,
    //         'quotas' => 3,
    //     ];

    //     // Mock del servicio de pago
    //     $paymentServiceMock = Mockery::mock(\App\Services\PaymentService::class);
    //     $paymentServiceMock->shouldReceive('checkAmountAvailable')
    //         ->once()
    //         ->andReturn(false); // Simulando que no hay suficiente límite

    //     $this->app->instance(\App\Services\PaymentService::class, $paymentServiceMock);

    //     // Hacer la petición al controlador
    //     $response = $this->postJson('/api/payment', $request);

    //     // Verificar que el código de estado sea 404 (error)
    //     $response->assertStatus(404);

    //     // Verificar que el mensaje de error esté presente
    //     $response->assertJson([
    //         'Insufficient limit.',
    //     ]);
    // }
}
