<?php

return [
    "currency_api" => env("CURRENCY_API", "https://api.exchangerate-api.com/v4/latest/"),
    "currency_base" => env("CURRENCY_BASE", "USD"),
    "currency_cache_time" => env("CURRENCY_CACHE_TIME", 60),
];