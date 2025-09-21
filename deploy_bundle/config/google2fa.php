<?php

return [
    /*
     * Auth container binding
     */
    'auth' => 'auth',

    /*
     * Lifetime in minutes
     */
    'lifetime' => 0, // Mantém válido até logout

    /*
     * Renew lifetime at every new request
     */
    'keep_alive' => true,

    /*
     * Auth Guard
     */
    'guard' => '',

    /*
     * 2FA verified session var
     */
    'session_var' => 'google2fa',

    /*
     * One time password window
     */
    'window' => 1, // 30 segundos antes e depois

    /*
     * Forbid user to reuse One Time Passwords
     */
    'forbid_old_passwords' => false,

    /*
     * User's table column for google2fa secret
     */
    'otp_secret_column' => 'two_factor_secret',

    /*
     * One Time Password View
     */
    'view' => 'auth.2fa',

    /*
     * One Time Password error message
     */
    'error_messages' => [
        'wrong_otp' => "O código está incorreto.",
        'cannot_be_empty' => 'O código não pode estar vazio.',
        'unknown' => 'Ocorreu um erro desconhecido.',
    ],

    /*
     * Throw exceptions or just fire events?
     */
    'throw_exceptions' => true,

    /*
     * Which image backend to use for generating QR codes?
     */
    'qrcode_image_backend' => 'svg',
];