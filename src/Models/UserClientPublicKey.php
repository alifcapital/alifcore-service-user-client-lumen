<?php namespace AlifCapital\UserServiceClient\Models;


use Illuminate\Database\Eloquent\Model;

class UserClientPublicKey extends Model
{
    protected $table = 'user_client_public_keys';

    const STATUS_INACTIVE   = 0;
    const STATUS_ACTIVE     = 1;

    const STATUS_ARRAY = [
        self::STATUS_INACTIVE   => ['id' => self::STATUS_INACTIVE,  'label' => 'Inactive'],
        self::STATUS_ACTIVE     => ['id' => self::STATUS_ACTIVE,    'label' => 'Active'],
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:m:s',
        'updated_at' => 'datetime:d-m-Y H:m:s',
    ];

    protected $fillable = [
        'public_key',
        'status'
    ];

    protected $hidden = ['created_at'];

}
