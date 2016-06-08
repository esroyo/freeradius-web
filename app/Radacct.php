<?php

namespace FreeradiusWeb;

use Illuminate\Database\Eloquent\Model;

class Radacct extends Model
{
    public $timestamps = false;

    protected $table = 'radacct';
    protected $casts = [
        'radacctid' => 'integer',
        'nasportid' => 'integer',
        'acctinterval' => 'integer',
        'interval' => 'integer',
        'acctsessiontime' => 'integer',
        'sessiontime' => 'integer',
        'acctinputoctets' => 'integer',
        'inputoctets' => 'integer',
        'acctoutputoctets' => 'integer',
        'outputoctets' => 'integer',
    ];
    protected $dates = [
        'acctstarttime',
        'starttime',
        'acctupdatetime',
        'updatetime',
        'acctstoptime',
        'stoptime',
    ];
    protected $fillable = [];
}
