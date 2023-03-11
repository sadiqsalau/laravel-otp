<?php

namespace Sadiqsalau\LaravelOtp\Stores;

use Illuminate\Support\Facades\Session;
use Sadiqsalau\LaravelOtp\Contracts\OtpStoreInterface as Store;


class SessionStore implements Store
{
    /**
     * Otp session key
     *
     * @var string
     */
    protected string $key;

    public function __construct()
    {
        $this->key = config('otp.store_key');
    }

    /**
     * Store Otp in session
     *
     * @param array $otp
     * @return void
     */
    public function put($otp)
    {
        Session::put($this->key, $otp);
    }

    /**
     * Get Otp in session
     *
     * @return array|null
     */
    public function retrieve()
    {
        return Session::get($this->key);
    }

    /**
     * Remove Otp from session
     *
     * @return void
     */
    public function clear()
    {
        Session::remove($this->key);
    }
}
