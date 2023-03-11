# Laravel OTP

## Introduction

OTP Package for Laravel using class based system

## Installation

Install via composer

```bash
composer require sadiqsalau/laravel-otp
```

Publish config file

```bash
php artisan vendor:publish --provider="Sadiqsalau\LaravelOtp\OtpServiceProvider"
```

## Usage

### Generate OTP

```bash
php artisan make:otp {name}
```

A new Otp class will be generated into the `app/Otp` folder. e.g

```bash
php artisan make:otp UserRegistrationOtp
```

Otp must implement the `process` method which will be called after verification.

```php
<?php

namespace App\Otp;

use Sadiqsalau\LaravelOtp\Contracts\OtpInterface as Otp;

class UserRegistrationOtp implements Otp
{
    /**
     * Constructs Otp class
     */
    public function __construct(
        protected $username
    ) {
        //
    }

    /**
     * Processes the Otp
     *
     * @return mixed
     */
    public function process()
    {
        return $this->username;
    }
}

```

### Sending OTP

```php
<?php
use Sadiqsalau\LaravelOtp\Facades\Otp;
use Sadiqsalau\LaravelOtp\Contracts\OtpInterface as Otp;

Otp::send(Otp $otp, $notifiable)
```

- `$otp`: The otp to send.
- `$notifiable`: AnonymousNotifiable or Notifiable instance.

```php
use Sadiqsalau\LaravelOtp\Facades\Otp;
use App\Otp\UserRegistrationOtp;
use Illuminate\Support\Facades\Notification;

Route::post('/register', function(Request $request){
    //...
    return Otp::send(
        new UserRegistrationOtp(
            username: $request->username
        ),
        Notification::route('mail', $request->email)
    );
});
```

Returns

```php
['status' => Otp::OTP_SENT] // Success: otp.sent
```

### Verify OTP

```php
<?php
use Sadiqsalau\LaravelOtp\Facades\Otp;

Otp::attempt(string $code)
```

- `$code`: The otp code to compare against.

Returns

```php
['status' => Otp::EMPTY_OTP]        // Error: otp.empty
['status' => Otp::EXPIRED_OTP]      // Error: otp.expired
['status' => Otp::MISMATCHED_CODE]  // Error: otp.mismatched
['status' => Otp::OTP_PROCESSED, 'result'=>[]] // Success: otp.processed
```

The `result` key contains the returned value of the `process` method of any Otp class

```php
use Sadiqsalau\LaravelOtp\Facades\Otp;

Route::get('/otp/verify', function (Request $request) {
    //...
    $otp = Otp::attempt($request->code);

    if($otp['status'] != Otp::OTP_PROCESSED)
    {
        return __($otp['status']);
    }
    else {
        return $otp['result'];
    }
});
```

### Resend OTP

```php
<?php
use Sadiqsalau\LaravelOtp\Facades\Otp;

Otp::update();
```

Returns

```php
['status' => Otp::EMPTY_OTP]    // Error: otp.empty
['status' => Otp::OTP_SENT]     // Success: otp.sent
```

```php
use Sadiqsalau\LaravelOtp\Facades\Otp;

Route::get('/otp/resend', function () {
    return Otp::update();
});
```

## TODO

Update README

## Contribution

Contributions are welcomed.
