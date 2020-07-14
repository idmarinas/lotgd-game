
<?php

use Laminas\Validator;

return [
    [
        'name' => 'newdaycron',
        'required' => false,
        'filters' => [],
        'validators' => [
            ['name' => Validator\NotEmpty::class],
            ['name' => Validator\Digits::class]
        ]
    ]
];
