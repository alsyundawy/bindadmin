<?php

declare(strict_types=1);

return [
    'zone_path' => env('BIND_ZONE_PATH', '/etc/bind/zones'),
    'named_conf' => env('BIND_NAMED_CONF', '/etc/bind/named.conf.local'),
    'rndc' => env('BIND_RNDC', '/usr/sbin/rndc'),
    'rndc_key' => env('BIND_RNDC_KEY', '/etc/bind/rndc.key'),
    'default_ttl' => (int) env('BIND_DEFAULT_TTL', 3600),
    'default_ns1' => env('BIND_DEFAULT_NS1', 'ns1.example.com.'),
    'default_ns2' => env('BIND_DEFAULT_NS2', 'ns2.example.com.'),
    'default_email' => env('BIND_DEFAULT_EMAIL', 'hostmaster.example.com.'),
    'demo_mode' => filter_var(env('BIND_DEMO_MODE', 'true'), FILTER_VALIDATE_BOOLEAN),
];
