<?php

namespace SadiqSalau\LaravelOtp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static identifier(mixed $identifier)
 * @method static array send(\SadiqSalau\LaravelOtp\Contracts\OtpInterface $otp, mixed $notifiable)
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
    const OTP_EMPTY = 'otp.empty';

    /**
     * Constant representing an expired otp.
     *
     * @var string
     */
    const OTP_EXPIRED = 'otp.expired';

    /**
     * Constant representing a mismatched code.
     *
     * @var string
     */
    const OTP_MISMATCHED = 'otp.mismatched';

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'otp';
    }
}
