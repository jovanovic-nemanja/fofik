<?php
if (! function_exists('frontendDate')) {
    function frontendDate(): String
    {
        return app(\App\Repositories\Format\GetDateFormat::class)->getFrontendDate();
    }
}
if (! function_exists('frontendTime')) {
    function frontendTime(): String
    {
        return app(\App\Repositories\Format\GetDateFormat::class)->getFrontendTime();
    }
}
if (! function_exists('carbonTime')) {
    function carbonTime(): String
    {
        return app(\App\Repositories\Format\GetDateFormat::class)->getCarbonTime();
    }
}

if (! function_exists('carbonFullDateWithText')) {
    function carbonFullDateWithText(): String
    {
        return app(\App\Repositories\Format\GetDateFormat::class)->getCarbonFullDateWithText();
    }
}

if (! function_exists('carbonDateWithText')) {
    function carbonDateWithText(): String
    {
        return app(\App\Repositories\Format\GetDateFormat::class)->getCarbonDateWithText();
    }
}

if (! function_exists('carbonDate')) {
    function carbonDate(): String
    {
        return app(\App\Repositories\Format\GetDateFormat::class)->getCarbonDate();
    }
}

if (! function_exists('isDemo')) {
    function isDemo(): String
    {
        return app()->environment() == "demo" ? 1 : 0;
    }
}