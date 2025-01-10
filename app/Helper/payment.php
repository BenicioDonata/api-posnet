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