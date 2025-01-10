<?php

namespace App\Services;

use App\Models\{Card, Payment};
use Illuminate\Support\Facades\DB;
use Exception;


class PaymentService
{

    public function calculateAmount($request) {

        // SE PODRIA PONER VARIABLES DE ENTORNO O UNA TABLA CON EL % de INCREMENTO

        $amount = $request->amount;
        $quotas = $request->quota;

        $incremetPercent = ($quotas * 3 ) - 3 ; //SE LES RESTA EL 3% DE LA 1ra CUOTA

        $subTotalAmount = ($amount * $incremetPercent) / 100; //CALCULO EL increment percent del monto comprado

        return $amount + $subTotalAmount; //RETORNO EL MONTO TOTAL MAS EL INCREMENTO POR CUOTAS

    }

    public function checkAmountAvailable($request, $totalAmount) {

        try{

            $card = Card::getCardByNumber($request->number_card);

            if($card->enabled_amount_limit < $totalAmount){
                return false;
            }

            return true;

        }catch (\Exception $e) {
            throw new Exception(sprintf("ERROR: '%s'", $e->getMessage()));
        }
    }

    public function doPayment($request, $totalAmount) {

        $number = $request->number_card;
        $amountPerQuota = $totalAmount / $request->quota; //calculo el valor por cuota

        try
        {

            $card = Card::getCardByNumber($number); //obtengo el objeto card con el objeto user por la relacion del modelo

            DB::beginTransaction();

            //guardo el pago
            $payment = New Payment();
            $payment->card()->associate(Card::find($card->id));
            $payment->amount = $totalAmount;
            $payment->save();

            DB::commit();

            //retorno los valores
            return [
                'Name and LastName' => $card->user->name . $card->user->lastname,
                'Amuount' => $totalAmount,
                'Amount per Quota' => $amountPerQuota
            ];

        } catch (\Exception $e) {

            DB::rollback();

            throw new Exception(sprintf("ERROR: '%s'", $e->getMessage()));
        }

    }


}
