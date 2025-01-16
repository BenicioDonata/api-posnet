<?php

namespace App\Http\Controllers\Posnet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\{CardService, PaymentService};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PosnetController extends Controller
{
    private $cardService;
    private $paymentService;

    public function __construct(CardService $cardService, PaymentService $paymentService)
    {
        $this->cardService = $cardService;
        $this->paymentService = $paymentService;
    }

    public function addCard(Request $request) : JsonResponse
    {

        try {

            if (validate_request($request)) {
                return response()->json(["Request Error."], 500);
            }

            //se podria hacer mas performante con una tabla de tarjetas habilitadas para validar el nombre de la entidad
            if (validate_entity_card($request)) {
                return response()->json(["Entity name banck Error."], 500);
            }

            //vlido la cantidad de digitos de la tarjeta
            if(strlen($request->number_card) < 8 || strlen($request->number_card) > 8 ){
                return response()->json(["Invalid number card."], 500);
            }

            $card = $this->cardService->addCard($request);

            return response()->json(['Success' => $card], 201);

        }catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    //EN UNA HORA MAS HICE ESTO 
    public function doPayment(Request $request) : JsonResponse
    {

        $totalAmount = $request->amount;

        try {

            if (validate_request_payment($request)) {
                return response()->json(["Request Error."], 500);
            }

            //o usar pra validar el requets
            // try {
            //     $request->validate([
            //         'content' => 'required|string|max:300',
            //     ]);
            // } catch (ValidationException $e) {
            //     return response()->json($e->errors(), 422);
            // }

            //valido que monto no sea menor o igual a cero
            if($totalAmount <= 0) {
                return response()->json(["Amount is zero."], 500);
            }

            //VALIdo que cantidad de cuotas no sea igual menor a cero
            if($request->quotas <= 0) {
                return response()->json(["Quota is zero."], 500);
            }

            // Si el pago es en 1 cuota, no se genera ningún recargo, de lo contrario, el monto se
            // incrementará en
            // un 3% por cada cuota superior a 1. (Ejemplo: Pagar en 5 cuotas representará un 12% de
            // incremento).

            if($request->quotas > 1){
                $totalAmount = $this->paymentService->calculateAmount($request);
            }

            // El POSNET debe chequear que la tarjeta tenga limite suficiente para poder efectuar el
            // pago junto
            // con el recargo, si hubiese. En caso de éxito, debe generar y retornar (no mostrar) los
            // datos de un ticket donde
            // consten los siguientes :
            // ▪ Nombre y apellido del cliente.
            // ▪ Monto total a pagar.
            // ▪ Monto de cada cuota.
            // Si la operación no tuvo éxito, se retornará una excepcion controlada.

            $amountAvailable = $this->paymentService->checkAmountAvailable($request, $totalAmount);

            if(!$amountAvailable){
                return response()->json(["Insufficient limit."], 404);
            }

            $this->paymentService->doPayment($request, $totalAmount);

            return response()->json(['Message' => 'Payment Success'], 201);

        }catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
