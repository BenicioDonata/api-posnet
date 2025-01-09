<?php

namespace App\Services;

use App\Models\{Card, Payment};
use Illuminate\Support\Facades\DB;
use Exception;


class PaymentService
{

    public function doPayment($request) {

        try
        {
            DB::beginTransaction();

            //TODO
            //BUSCAR POR NUMBER CARD DENTRO DEL MODELO CARD Y OBTENER EL OBJETO CARD PARA DESPUES ASOCIARLO AL PAYMENT
                

            DB::commit();

            //TODO
            //RETORNAR PAYMENT ;


        } catch (\Exception $e) {

            DB::rollback();

            throw new Exception(sprintf("ERROR: '%s'", $e->getMessage()));
        }

    }


}
