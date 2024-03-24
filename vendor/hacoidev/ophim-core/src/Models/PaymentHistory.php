<?php

namespace Ophim\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Ophim\Core\Traits\HasFactory;

class PaymentHistory extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'payment_histories';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    public function episode()
    {
        return $this->hasOne(Episode::class, 'id', 'episode_id');
    }
}
