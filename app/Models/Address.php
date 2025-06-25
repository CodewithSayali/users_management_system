<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Addresstype;
class Address extends Model
{
    protected $table='addresses';
    use HasFactory;

    protected $fillable = [
        'id ',
        'first_name',
        'user_xid ',
        'address_type',
        'door_street',
        'landmark',
        'city_xid',
        'country_xid ',
        'user_xid ',
        'state_xid ',
        'is_primary',
        'created_at',
        'updated_at',
    ];

public function addressType()
{
    return $this->belongsTo(Addresstype::class, 'addresstype_xid');
}
    
}
