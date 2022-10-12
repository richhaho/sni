<?php

return [

    'pdf' => [
        'enabled' => true,
        'binary' => base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
        'timeout' => false,
        'options' => ['encoding' => 'utf8', 'print-media-type' => true, 'margin-top' => 12,
            'margin-right' => 16,
            'margin-bottom' => 12,
            'margin-left' => 16, ],
        'env' => [],
    ],
    'image' => [
        'enabled' => true,
        'binary' => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => [],
        'env' => [],
    ],

];
