UserAuthenticator
=======
[![Build Status](https://travis-ci.org/rarog/user-authenticator.svg?branch=master)](https://travis-ci.org/rarog/user-authenticator)
[![Coverage Status](https://coveralls.io/repos/github/rarog/user-authenticator/badge.svg?branch=master)](https://coveralls.io/github/rarog/user-authenticator?branch=master)

Based on ZfcUser by Evan Coury and the ZF-Commons team

Introduction
------------

UserAuthenticator is a user registration and authentication module for Laminas.
UserAuthenticator provides the foundations for adding user authentication and
registration to your Laminas site. It is designed to be very simple and easy to
extend.

Requirements
------------

* [Laminas](https://getlaminas.org/).

If you are looking for versions based on ZendFramework, please look to the original [ZfcUser](https://github.com/ZF-Commons/ZfcUser/).

Features / Goals
----------------

* Authenticate via username, email, or both (can opt out of the concept of
  username and use strictly email)
* User registration
* Forms protected against CSRF
* Robust event system to allow for extending
* Provide ActionController plugin and view helper

Installation
------------

### Main Setup

#### Via composer

Install the model using [Composer](https://getcomposer.org):

```bash
$ composer require rarog/user-authenticator
```

#### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return [
        'modules' => [
            // ...
            'UserAuthenticator',
        ],
        // ...
    ];
    ```

2. Then import the fitting SQL schema located in `./vendor/rarog/user-authenticator/data/`.

### Post-Install: Laminas\Db

1. If you do not already have a valid Laminas\Db\Adapter\Adapter in your service
   manager configuration, put the following in `./config/autoload/database.local.php`:

```php
<?php
return array(
    'db' => array(
        'driver'    => 'PdoMysql',
        'hostname'  => 'changeme',
        'database'  => 'changeme',
        'username'  => 'changeme',
        'password'  => 'changeme',
    ),
    'service_manager' => array(
        'factories' => array(
            \Laminas\Db\Adapter\Adapter::class => \Laminas\Db\Adapter\AdapterServiceFactory::class,
        ),
    ),
);

```

Navigate to http://yourproject/user and you should land on a login page.

Password Security
-----------------

**DO NOT CHANGE THE PASSWORD HASH SETTINGS FROM THEIR DEFAULTS** unless A) you
have done sufficient research and fully understand exactly what you are
changing, **AND** B) you have a **very** specific reason to deviate from the
default settings.

If you are planning on changing the default password hash settings, please read
the following:

- [PHP Manual: crypt() function](http://php.net/manual/en/function.crypt.php)
- [Securely Storing Passwords in PHP by Adrian Schneider](http://www.syndicatetheory.com/labs/securely-storing-passwords-in-php)

The password hash settings may be changed at any time without invalidating existing
user accounts. Existing user passwords will be re-hashed automatically on their next
successful login.

**WARNING:** Changing the default password hash settings can cause serious
problems such as making your hashed passwords more vulnerable to brute force
attacks or making hashing so expensive that login and registration is
unacceptably slow for users and produces a large burden on your server(s). The
default settings provided are a very reasonable balance between the two,
suitable for computing power in 2013.

Options
-------

The ZfcUser module has some options to allow you to quickly customize the basic
functionality. After installing ZfcUser, copy
`./vendor/rarog/user-authenticator/config/user-authenticator.global.php.dist` to
`./config/autoload/user-authenticator.global.php` and change the values as desired.

The following options are available:

- **user_entity_class** - Name of Entity class to use. Useful for using your own
  entity class instead of the default one provided. Default is
  `\UserAuthenticator\Model\User::class`.
- **enable_username** - Boolean value, enables username field on the
  registration form. Default is `false`.
- **auth_identity_fields** - Array value, specifies which fields a user can
  use as the 'identity' field when logging in.  Acceptable values: username, email.
- **enable_display_name** - Boolean value, enables a display name field on the
  registration form. Default value is `false`.
- **enable_registration** - Boolean value, Determines if a user should be
  allowed to register. Default value is `true`.
- **login_after_registration** - Boolean value, automatically logs the user in
  after they successfully register. Default value is `false`.
- **use_registration_form_captcha** - Boolean value, determines if a captcha should
  be utilized on the user registration form. Default value is `true`. (Note,
  right now this only utilizes a weak Laminas\Text\Figlet CAPTCHA, but I have plans
  to make all Laminas\Captcha adapters work.)
- **login_form_timeout** - Integer value, specify the timeout for the CSRF security
  field of the login form in seconds. Default value is 300 seconds.
- **user_form_timeout** - Integer value, specify the timeout for the CSRF security
  field of the registration form in seconds. Default value is 300 seconds.
- **use_redirect_parameter_if_present** - Boolean value, if a redirect GET
  parameter is specified, the user will be redirected to the specified URL if
  authentication is successful (if present, a GET parameter will override the
  login_redirect_route specified below).
- **login_redirect_route** String value, name of a route in the application
  which the user will be redirected to after a successful login.
- **logout_redirect_route** String value, name of a route in the application which
  the user will be redirected to after logging out.
- **password_cost** - This should be an integer between 4 and 31. The number
  represents the base-2 logarithm of the iteration count used for hashing.
  Default is `10` (about 10 hashes per second on an i5).
- **enable_user_state** - Boolean value, enable user state usage. Should user's
  state be used in the registration/login process?
- **default_user_state** - Integer value, default user state upon registration.
  What state user should have upon registration?
- **allowed_login_states** - Array value, states which are allowing user to login.
  When user tries to login, is his/her state one of the following? Include null if
  you want user's with no state to login as well.

Changing Registration Captcha Element
-------------------------------------

**NOTICE** These instructions are currently out of date.

By default, the user registration uses the Figlet captcha engine.  This is
because it's the only one that doesn't require API keys.  It's possible to change
out the captcha engine with DI.  For example, to change to Recaptcha, you would
add this to one of your configuration files (global.config.php,
module.config.php, or a dedicated recaptcha.config.php):

```php
<?php
// ./config/autoload/recaptcha.config.php
return [
    'di' => [
        'instance' => [
            'alias' => [
                // OTHER ELEMENTS....
                'recaptcha_element' => \Laminas\Form\Element\Captcha::class,
            ],
            'recaptcha_element' => [
                'parameters' => [
                    'spec' => 'captcha',
                    'options' => [
                        'label' => '',
                        'required' => true,
                        'order' => 500,
                        'captcha' => [
                            'captcha' => 'ReCaptcha',
                            'privkey' => RECAPTCHA_PRIVATE_KEY,
                            'pubkey' => RECAPTCHA_PUBLIC_KEY,
                        ],
                    ],
                ],
            ],
            \UserAuthenticator\Form\Register::class => [
                'parameters' => [
                    'captcha_element' => 'recaptcha_element',
                ],
            ],
        ],
    ],
];

```