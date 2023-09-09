<?php

namespace SadiqSalau\LaravelOtp\Stores;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use SadiqSalau\LaravelOtp\Contracts\OtpStoreInterface as Store;


class CacheStore implements Store
{
    /**
     * Otp cache key
     *
     * @var string
     */
    protected string $key;

    /**
     * Instantiate Cache store
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->key = config('otp.store_key') . '-' . md5($request->ip());
    }

    /**
     * Store Otp in cache
     *
     * @param array $otp
     * @return void
     */
    public function put($otp)
    {
        Cache::put(
            $this->key,
            $otp,
            $otp['expires']
        );
    }

    /**
     * Get Otp in cache
     *
     * @return array|null
     */
    public function retrieve()
    {
        return Cache::get($this->key) ?: null;
    }

    /**
     * Remove Otp from cache
     *
     * @return void
     */
    public function clear()
    {
        Cache::forget($this->key);
    }
}
