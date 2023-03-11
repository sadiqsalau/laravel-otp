<?php

namespace Sadiqsalau\LaravelOtp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array send(\Sadiqsalau\LaravelOtp\Contracts\OtpInterface $otp, mixed $notifiable)
 * @method static array attempt(string $code)
 * @method static array update()
 * @method static static clear()
 */
class Otp extends Facade
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
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'otp';
    }
}
