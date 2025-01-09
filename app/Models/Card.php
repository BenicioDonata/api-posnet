<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Card extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'bank_entity_name',
        'number_card',
        'enabled_amount_limit',
    ];

    protected $table = "card";


    public function user()
    {
        return $this->hasOne('App\Models\User');
    }

    public function payment()
    {
        return $this->hasOne('App\Models\Payment');
    }


}
