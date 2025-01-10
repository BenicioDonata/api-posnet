<?php

if (!function_exists('validate_request_payment')) {

function validate_request_payment($request)
{

    $rules = [
        'number_card',
        'amount',
        'quotas'
    ];

    $validator = validator()->make($request->all(),$rules);

    if($validator->fails()) {
        return true;
    }
}
}

if (!function_exists('validate_count_quotas')) {

    function validate_count_quotas($request)
    {
        $quotas = ['1','2','3','4','5','6'];
    
        if(!in_array($request->quotas, $quotas)){
            return false;
           }
        return true;
    }
    }


    if (!function_exists('calculateAmount')) {

        function calculateAmount($request)
        {
            $amount = $request->amount; //1000
            $quotas = $request->quotas; //3

            $incremetPercent = ($quotas * 3 ) - 3 ; //SE LES RESTA EL 3% DE LA 1ra CUOTA

            $subTotalAmount = ($amount * $incremetPercent) / 100; //CALCULO EL increment percent del monto comprado

            $total = $amount + $subTotalAmount; //RETORNO EL MONTO TOTAL MAS EL INCREMENTO POR CUOTAS

            return $total;
        }
    }