<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Addresstype;
use App\Models\City;
use App\Models\States;
class Address extends Model
{
    protected $table='addresses';
    use HasFactory;

    protected $fillable = [
        'id ',
        'first_name',
        'user_xid ',
        'addresstype_xid',
        'door_street',
        'landmark',
        'city_xid',
        'country_xid',
        'state_xid',
        'is_primary',
        'created_at',
        'updated_at',
    ];

public function addressType()
{
    return $this->belongsTo(Addresstype::class, 'addresstype_xid');
}
public function state()
{
    return $this->belongsTo(States::class, 'state_xid');
}
public function city()
{
    return $this->belongsTo(City::class, 'city_xid');
}
    
}
