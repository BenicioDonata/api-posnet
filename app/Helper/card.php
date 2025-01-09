<?php

if (!function_exists('validate_request')) {

    function validate_request($request)
    {

        $rules = [
            'bank_entity_name',
            'number_card',
            'dni',
            'name',
            'lastname'
        ];

        $validator = validator()->make($request->all(),$rules);

        if($validator->fails()) {
            return true;
        }
    }
}

if (!function_exists('validate_entity_card')) {

    function validate_entity_card($request)
    {

        $entities = [
            'Visa',
            'Amex'
        ];

       if(!in_array($request->bank_entity_name, $entities)){
        return false;
       }
    }
}