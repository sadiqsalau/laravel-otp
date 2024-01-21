# Laravel OTP

## Introduction

OTP Package for Laravel using class based system. Every Otp is a class that does something. For example, an `EmailVerificationOtp` which will mark the account as verified.

## Installation

Install via composer

```bash
composer require sadiqsalau/laravel-otp
```

Publish config file

```bash
php artisan vendor:publish --provider="SadiqSalau\LaravelOtp\OtpServiceProvider"
```

## Usage

### Generate OTP

```bash
php artisan make:otp {name}
```

A new Otp class will be generated into the `app/Otp` directory. e.g

```bash
php artisan make:otp UserRegistrationOtp
```

Every Otp must implement the `process` method which will be called after verification. There the Otp can perform the necessary action and return any result.

```php
<?php

namespace App\Otp;

use SadiqSalau\LaravelOtp\Contracts\OtpInterface as Otp;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserRegistrationOtp implements Otp
{
    /**
     * Constructs Otp class
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
        //
    }

    /**
     * Processes the Otp
     *
     * @return User
     */
    public function process()
    {
        /** @var User */
        $user = User::unguarded(function () {
            return User::create([
                'name'                  => $this->name,
                'email'                 => $this->email,
                'password'              => Hash::make($this->password),
                'email_verified_at'     => now(),
            ]);
        });

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }
}

```

### Sending OTP

```php
<?php
use SadiqSalau\LaravelOtp\Facades\Otp;

Otp::identifier($identifier)->send($otp, $notifiable);
```

- `$otp`: The otp to send.
- `$notifiable`: AnonymousNotifiable or Notifiable instance.

```php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

use SadiqSalau\LaravelOtp\Facades\Otp;

use App\Models\User;
use App\Otp\UserRegistrationOtp;

Route::post('/register', function(Request $request){
    $request->validate([
        'name'          => ['required', 'string', 'max:255'],
        'email'         => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
        'password'      => ['required',  Rules\Password::defaults()],
    ]);

    $otp = Otp::identifier($request->email)->send(
        new UserRegistrationOtp(
            name: $request->name,
            email: $request->email,
            password: $request->password
        ),
        Notification::route('mail', $request->email)
    );

    return __($otp['status']);
});
```

Returns

```php
['status' => Otp::OTP_SENT] // Success: otp.sent
```

### Verify OTP

```php
<?php
use SadiqSalau\LaravelOtp\Facades\Otp;

Otp::identifier($identifier)->attempt($code);
```

- `$code`: The otp code to compare against.

Returns

```php
['status' => Otp::OTP_EMPTY]        // Error: otp.empty
['status' => Otp::OTP_MISMATCHED]  // Error: otp.mismatched
['status' => Otp::OTP_PROCESSED, 'result'=>[]] // Success: otp.processed
```

The `result` key contains the returned value of the `process` method of the Otp class

```php
<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use SadiqSalau\LaravelOtp\Facades\Otp;

Route::post('/otp/verify', function (Request $request) {

    $request->validate([
        'email'    => ['required', 'string', 'email', 'max:255'],
        'code'     => ['required', 'string']
    ]);

    $otp = Otp::identifier($request->email)->attempt($request->code);

    if($otp['status'] != Otp::OTP_PROCESSED)
    {
        abort(403, __($otp['status']));
    }

    return $otp['result'];
});
```

### Verify OTP without clearing from cache

```php
<?php
use SadiqSalau\LaravelOtp\Facades\Otp;

Otp::identifier($identifier)->check($code);
```

- `$code`: The otp code to compare against.

Returns

```php
['status' => Otp::OTP_EMPTY]        // Error: otp.empty
['status' => Otp::OTP_MISMATCHED]  // Error: otp.mismatched
['status' => Otp::OTP_MATCHED] // Success: otp.matched
```

### Resend OTP

```php
<?php
use SadiqSalau\LaravelOtp\Facades\Otp;

Otp::identifier($identifier)->update();
```

Returns

```php
['status' => Otp::OTP_EMPTY]    // Error: otp.empty
['status' => Otp::OTP_SENT]     // Success: otp.sent
```

```php
<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use SadiqSalau\LaravelOtp\Facades\Otp;

Route::post('/otp/resend', function (Request $request) {

    $request->validate([
        'email'    => ['required', 'string', 'email', 'max:255']
    ]);

    $otp = Otp::identifier($request->email)->update();

    if($otp['status'] != Otp::OTP_SENT)
    {
        abort(403, __($otp['status']));
    }
    return __($otp['status']);
});
```

### Setting Identifier

Every method of the OTP class requires setting an identifier to uniquely identify the Otp.

```php
<?php
use SadiqSalau\LaravelOtp\Facades\Otp;

Otp::identifier($request->email)->send(...);
```

```php
<?php
use SadiqSalau\LaravelOtp\Facades\Otp;

Otp::identifier($identifier)->send(...);
Otp::identifier($identifier)->attempt(...);
Otp::identifier($identifier)->update();
Otp::identifier($identifier)->check(...);
```

## Config

Config file can be found at `config/otp.php` after publishing the package

- `format` - Format of generated OTP code (`numeric` | `alphanumeric` | `alpha`)
- `length` - Length of generated OTP code
- `expires` - Number of minutes before OTP expires,
- `notification` - Custom notification class to use, default is `SadiqSalau\LaravelOtp\OtpNotification`

## Translations

The package doesn't provide translations out of the box, but here is an example.
Create a new translation file: `lang/en/otp.php`

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the OTP broker
    |
    */

    'sent'          => 'We have sent your OTP code!',
    'empty'         => 'No OTP!',
    'matched'       => 'OTP code verified!',
    'mismatched'    => 'Mismatched OTP code!',
    'processed'     => 'OTP was successfully processed!'
];

```

Then translate the status

```php
return __($otp['status'])
```

## API

- `Otp::identifier(mixed $identifier)` - Set OTP identifier

- `Otp::send(OtpInterface $otp, mixed $notifiable)` - Send OTP to a notifiable

- `Otp::attempt(string $code)` - Attempt OTP code, returns the result of calling the `process` method of the OTP

- `Otp::check(string $code)` - Compares the code against current OTP, this doesn't process or clear the OTP

- `Otp::update()` - Resend and update current OTP

- `Otp::clear()` - Remove OTP

- `Otp::useGenerator(callable $callback)` - Set custom generator to use, generator will be called with `$format` and `$length`

- `Otp::generateOtpCode($format, $length)` - Generates the OTP code

## Contribution

Contributions are welcomed.
