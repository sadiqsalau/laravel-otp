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
        $this->store->put(
            $data = $this->createOtpData(
                $otp,
                $notifiable
            )
        );


        return with(
            config('otp.notification') ?:
                OtpNotification::class,

            function ($notification)
            use ($data) {

                // Send notification
                $data['notifiable']->notify(
                    new $notification($data['code'])
                );

                return ['status' => static::OTP_SENT];
            }
        );
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
     * Attempt Otp code
     *
     * @param string $code
     * @return array
     */
    public function attempt($code)
    {
        // Otp exists?
        if (!$data = $this->store->retrieve())
            return ['status' => static::OTP_EMPTY];


        // Has it expired?
        else if (now() > $data['expires']) {

            // Clear the OTP
            $this->store->clear();

            return ['status' => static::OTP_EXPIRED];
        }

        // Is the code correct?
        else if ($data['code'] != $code)
            return ['status' => static::OTP_MISMATCHED];

        // Process the Otp
        else {
            return with(
                $data['otp']->process(),

                function ($result) {

                    $this->store->clear();

                    return [
                        'status' => static::OTP_PROCESSED,
                        'result' => $result
                    ];
                }
            );
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
     * Generates Otp code
     *
     * @param string $format
     * @param int $length
     * @return string
     */
    public function generateOtpCode(
        $format,
        $length
    ) {
        return $this->customGenerator ? call_user_func(
            $this->customGenerator,
            $format,
            $length
        ) : $this->defaultGenerator($format, $length);
    }

    /**
     * Set custom Otp generator
     *
     * @param callable $callback
     * @return static
     */
    public function useGenerator($callback)
    {
        if (is_callable($callback)) {
            $this->customGenerator = $callback;
        }

        return $this;
    }


    /**
     * Otp default generator
     *
     * @param string $format
     * @param int $length
     * @return string
     */
    protected function defaultGenerator($format, $length)
    {
        return Randomize::chars($length)->{$format}()->generate();
    }

    /**
     * Set store identifier
     *
     * @param  mixed  $identifier
     * @return static
     */
    public function identifier($identifier)
    {
        if (method_exists($this->store, 'identifier')) {
            call_user_func([$this->store, 'identifier'], $identifier);
        }

        return $this;
    }
}
