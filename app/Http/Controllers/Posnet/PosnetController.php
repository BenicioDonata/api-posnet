<?php

namespace App\Http\Controllers\Posnet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\{CardService, PaymentService};

class PosnetController extends Controller
{
    private $cardService;
    private $paymentService;

   

    public function __construct(CardService $cardService, PaymentService $paymentService)
    {
        $this->cardService = $cardService;
        $this->paymentService = $paymentService;

       
    }

    public function addCard(Request $request)
    {

        try {

            if (validate_request($request)) {
                return response()->json(["Request Error."], 500);
            }

            //se podria hacer mas performante con una tabla de tarjetas habilitadas para validar el nombre de la entidad
            if (validate_entity_card($request)) {
                return response()->json(["Entity name banck Error."], 500);
            }

            $card = $this->cardService->addCard($request);

            return response()->json(['Success' => $card], 201);

        }catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function doPayment(Request $request)
    {

        try {

            $payment = $this->paymentService->doPayment($request);

            return response()->json(['Success' => $payment], 201);

        }catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
