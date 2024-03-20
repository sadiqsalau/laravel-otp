<?php

namespace SadiqSalau\LaravelOtp;

use HiFolks\RandoPhp\Randomize;
use SadiqSalau\LaravelOtp\Contracts\OtpBrokerInterface;
use SadiqSalau\LaravelOtp\Contracts\OtpInterface as Otp;
use SadiqSalau\LaravelOtp\OtpNotification;
use SadiqSalau\LaravelOtp\OtpStore;

class OtpBroker implements OtpBrokerInterface
{
    /**
     * Custom Otp generator
     *
     * @var callable|null
     */
    protected static $customGenerator;

    /**
     * Instantiates the broker
     *
     * @param OtpStore $store Otp store
     */
    public function __construct(
        protected OtpStore $store
    ) {
    }


    /**
     * Send Otp notification
     *
     * @param Otp $otp
     * @param mixed $notifiable
     * @return array
     */
    public function send(Otp $otp, $notifiable)
    {
        /** Notification class to use */
        $notification = config('otp.notification') ?: OtpNotification::class;

        $data = $this->createOtpData(
            $otp,
            $notifiable
        );

        // Send notification
        $notifiable->notify(
            new $notification($data)
        );

        // Store otp
        $this->store->put($data);

        return ['status' => static::OTP_SENT];
    }

    /**
     * Update current Otp
     *
     * @return array
     */
    public function update()
    {
        return ($data = $this->store->retrieve()) ?
            $this->send(
                $data['otp'],
                $data['notifiable']
            ) :
            ['status' => static::OTP_EMPTY];
    }

    /**
     * check Otp code without clearing
     *
     * @param string $code
     * @return array
     */
    public function check($code)
    {
        // Otp exists?
        if (!$data = $this->store->retrieve())
            return ['status' => static::OTP_EMPTY];

        // Is the code correct?
        else if ($data['code'] != $code)
            return ['status' => static::OTP_MISMATCHED];

        return [
            'status' => static::OTP_MATCHED,
        ];
    }

    /**
     * Attempt Otp code and clear
     *
     * @param string $code
     * @return array
     */
    public function attempt($code)
    {
        // Otp exists?
        if (!$data = $this->store->retrieve())
            return ['status' => static::OTP_EMPTY];

        // Is the code correct?
        else if ($data['code'] != $code)
            return ['status' => static::OTP_MISMATCHED];

        // Process the Otp
        else {
            $result = $data['otp']->process();

            // Clear the Otp
            $this->clear();

            return [
                'status' => static::OTP_PROCESSED,
                'result' => $result
            ];
        }
    }

    /**
     * Clears Otp
     *
     * @return static
     */
    public function clear()
    {
        $this->store->clear();

        return $this;
    }

    /**
     * Create Otp data
     *
     * @param Otp $otp
     * @param mixed $notifiable
     * @return array
     */
    protected function createOtpData(
        $otp,
        $notifiable
    ) {
        return [
            'otp'           => $otp,
            'notifiable'    => $notifiable,
            'code'          => $this->generateOtpCode(
                config('otp.format'),
                config('otp.length')
            ),
            'expires'       => now()->addMinutes(config('otp.expires'))
        ];
    }

    /**
     * Set store identifier
     *
     * @param  string  $identifier
     * @return static
     */
    public function identifier($identifier)
    {
        $this->store->identifier($identifier);

        return $this;
    }

    /**
     * Generates Otp code
     *
     * @param string $format
     * @param int $length
     * @return string
     */
    public static function generateOtpCode(
        $format,
        $length
    ) {
        return static::$customGenerator ? call_user_func(
            static::$customGenerator,
            $format,
            $length
        ) : static::defaultGenerator($format, $length);
    }

    /**
     * Set custom Otp generator
     *
     * @param callable $callback
     * @return static
     */
    public static function useGenerator($callback)
    {
        if (is_callable($callback)) {
            static::$customGenerator = $callback;
        }
    }


    /**
     * Otp default generator
     *
     * @param string $format
     * @param int $length
     * @return string
     * @throws \Exception
     */
    protected static function defaultGenerator($format, $length)
    {
        if (!in_array($format, ['numeric', 'alpha', 'alphanumeric'], true)) {
            throw new \Exception('Unknown OTP code format!');
        }
        return Randomize::chars($length)->{$format}()->generate();
    }
}
