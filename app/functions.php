<?php

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        $_token = md5(uniqid('', true));
        \request()->session()->set('csrf_token', $_token);
        return $_token;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}