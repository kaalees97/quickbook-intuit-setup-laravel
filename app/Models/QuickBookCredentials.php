<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickBookCredentials extends Model
{
    use HasFactory;
    protected $table="quickbook_credentials";
    protected $fillable = [
        'client_id',
        'client_secret',
        'redirect_uri',
        'access_token',
        'refresh_token',
        'realm_id',
        'base_uri',
        'api_uri',
        'others',
        'updating_time',
        'status',
    ];
}
