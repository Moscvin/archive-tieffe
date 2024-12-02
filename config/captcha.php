<?php
/*
 * Secret key and Site key get on https://www.google.com/recaptcha
 * */
return [
    'secret' => env('CAPTCHA_SECRET', '6LfhqKQUAAAAAB8IG_NuT_3A-dTyIZGVbdH9Sr9R'),
    'sitekey' => env('CAPTCHA_SITEKEY', '6LfhqKQUAAAAANoc87qWKPBzK3q0GY_QzF3SLaL6')
];