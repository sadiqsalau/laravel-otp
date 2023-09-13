<?php

namespace SadiqSalau\LaravelOtp\Contracts;

interface OtpStoreInterface
{
    /**
     * Set Otp identifier
     *
     * @param string $identifier
     * @return static
     */
    public function identifier($identifier);

    /**
     * Store Otp
     *
     * @param array $otp
     * @return void
     */
    public function put($otp);

    /**
     * Retrieve Otp in store
     *
     * @return array|null
     */
    public function retrieve();

    /**
     * Remove Otp from store
     *
     * @return void
     */
    public function clear();
}
