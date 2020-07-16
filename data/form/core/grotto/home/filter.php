
<?php

use Laminas\Validator;

return [
    [
        'name' => 'defaultskin',
        'required' => true,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
        ]
    ],
];
