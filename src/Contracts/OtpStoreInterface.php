<?php

namespace SadiqSalau\LaravelOtp\Contracts;

interface OtpStoreInterface
{
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
