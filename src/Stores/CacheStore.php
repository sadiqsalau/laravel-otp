<?php

namespace SadiqSalau\LaravelOtp\Stores;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use SadiqSalau\LaravelOtp\Contracts\OtpStoreInterface as Store;


class CacheStore implements Store
{
    /**
     * Store key
     *
     * @var string
     */
    protected string $key;

    /**
     * Store identifier
     *
     * @var string
     */
    protected string $identifier;

    /**
     * Instantiate Cache store
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->key = config('otp.store_key');
        $this->identifier = md5($request->ip());
    }

    /**
     * Put Otp in cache
     *
     * @param array $otp
     * @return void
     */
    public function put($otp)
    {
        Cache::put(
            $this->getCacheKey(),
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
        return Cache::get($this->getCacheKey()) ?: null;
    }

    /**
     * Remove Otp from cache
     *
     * @return void
     */
    public function clear()
    {
        Cache::forget($this->getCacheKey());
    }


    /**
     * Set identifier
     *
     * @param string $identifier
     * @return void
     */
    public function identifier($identifier)
    {
        $this->identifier = md5($identifier);
    }

    /**
     * Return the cache key
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->key . '_' . $this->identifier;
    }
}
