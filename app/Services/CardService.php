<?php

namespace App\Services;

use App\Models\{Card, User};
use Illuminate\Support\Facades\DB;
use Exception;


class CardService
{

    public function addCard($request) {

        try
        {
            DB::beginTransaction();

                $user = new User();
                $user->dni = $request->dni;
                $user->name = $request->name;
                $user->lastname = $request->lastname;
                $user->save();

                $card = new Card();
                $card->user()->associate(User::find($user->id));
                $card->bank_entity_name = $request->bank_entity_name;
                $card->number_card = $request->number_card;
                $card->save();

            DB::commit();

            return $card;


        } catch (\Exception $e) {

            DB::rollback();

            throw new Exception(sprintf("ERROR: '%s'", $e->getMessage()));
        }

    }


}
