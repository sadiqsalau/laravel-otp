<?php

namespace SadiqSalau\LaravelOtp;

use Illuminate\Support\Facades\Cache;
use SadiqSalau\LaravelOtp\Contracts\OtpStoreInterface as Store;


class OtpStore implements Store
{
    /**
     * Store key
     *
     * @var string
     */
    const STORE_KEY = 'otp';

    /**
     * Store identifier
     *
     * @var string
     */
    protected string $identifier;

    /**
     * Set identifier
     *
     * @param string $identifier
     * @return static
     * @throws \Exception
     */
    public function identifier($identifier)
    {
        if (empty($identifier)) {
            throw new \Exception("OTP identifier is empty!");
        }
        $this->identifier = md5($identifier);

        return $this;
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
     * Return the cache key
     *
     * @return string
     * @throws \Exception
     */
    protected function getCacheKey()
    {
        if (!isset($this->identifier)) {
            throw new \Exception("No OTP identifier set!");
        }
        return static::STORE_KEY . '_' . $this->identifier;
    }
}
