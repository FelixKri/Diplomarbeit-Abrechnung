<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class fosUser extends Model implements Authenticatable
{
    use AuthenticableTrait;

    protected $table = 'fos_user'; 
    public $timestamps = false;

    protected $fillable = [
        'group_id', 'username', 'username_canonical', 'email', 'email_canonical', 
        'enabled', 'salt', 'password', 'last_login', 'locked', 'expired', 'expires_at', 
        'confirmation_token', 'password_requested_at', 'roles', 'credentials_expired', 
        'credentials_expire_at', 'last_name', 'first_name', 'sokr_key', 'iban', 'bic',
        'birthday', 'checkout_date'
    ];

    public function group(){
        return $this->belongsTo(Group::class);
    }

    public function emailCc(){
        return $this->hasMany(emailCc::class, 'user_id');
    }
}
