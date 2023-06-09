<?php

namespace Sadiqsalau\LaravelOtp\Contracts;

use Sadiqsalau\LaravelOtp\Contracts\OtpInterface as Otp;

interface OtpBrokerInterface
{
    /**
     * Constant representing a successfully sent otp.
     *
     * @var string
     */
    const OTP_SENT = 'otp.sent';

    /**
     * Constant representing a successfully processed otp.
     *
     * @var string
     */
    const OTP_PROCESSED = 'otp.processed';

    /**
     * Constant representing an empty otp.
     *
     * @var string
     */
    const EMPTY_OTP = 'otp.empty';

    /**
     * Constant representing an expired otp.
     *
     * @var string
     */
    const EXPIRED_OTP = 'otp.expired';

    /**
     * Constant representing a mismatched code.
     *
     * @var string
     */
    const MISMATCHED_CODE = 'otp.mismatched';

    /**
     * Send Otp notification
     *
     * @param Otp $otp
     * @param mixed $notifiable
     * @return array
     */
    public function send(
        Otp $otp,
        $notifiable
    );

    /**
     * Resend current OTP with updated expiration time
     *
     * @return array
     */
    public function update();

    /**
     * Attempt OTP code
     *
     * @param string $code
     * @return array
     */
    public function attempt($code);

    /**
     * Remove current Otp
     *
     * @return static
     */
    public function clear();
}
