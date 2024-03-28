<?php

namespace Ophim\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Ophim\Core\Traits\HasFactory;

class CashHistory extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'cash_histories';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    public function cashFromUser()
    {
        return $this->hasOne(Episode::class, 'id', 'episode_id');
    }
}
