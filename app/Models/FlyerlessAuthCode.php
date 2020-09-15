<?php

namespace Flyerless\FlyerlessClubManagement\Models;

use BristolSU\Support\User\Contracts\UserAuthentication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class FlyerlessAuthCode extends Model
{
    protected $table = 'flyerless_auth_codes';

    protected $fillable = [
        'api_key',
    ];

    protected $attributes = [
        'access_token' => '',
        'expires_at' => '2020-09-11 22:00:00',
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    /**
     * Scope auth codes to show to the user to select.
     *
     * These auth codes must both belong to the user, and have been created in the last 10 minutes.
     *
     * @param Builder $query
     */
    public function scopeValid(Builder $query)
    {
        $query->where('user_id', app(UserAuthentication::class)->getUser()->control_id)
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->orderBy('created_at', 'DESC');
    }

    public function isTokenValid()
    {
        return $this->expires_at->isFuture();
    }

//    public function setApiKeyAttribute($apiKey)
//    {
//        $this->attributes['api_key'] = Crypt::encrypt($apiKey);
//    }
//
//    public function getApiKeyAttribute($apiKey)
//    {
//        return Crypt::decrypt($apiKey);
//    }

//    public function setAccessTokenAttribute($accessToken)
//    {
//        $this->attributes['access_token'] = Crypt::encrypt($accessToken);
//    }
//
//    public function getAccessTokenAttribute($accessToken)
//    {
//        return Crypt::decrypt($accessToken);
//    }

}