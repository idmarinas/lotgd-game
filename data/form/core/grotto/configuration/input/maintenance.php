<?php

return [
    'name' => 'maintenance',
    'attributes' => [
        'id' => 'maintenance'
    ],
    'options' => [
        'label' => 'maintenance.title'
    ],
    'elements' => [
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'debug',
                'attributes' => [
                    'id' => 'debug',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'maintenance.debug.label',
                    'note' => 'maintenance.debug.note',
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'maintenance',
                'attributes' => [
                    'id' => 'maintenance',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'maintenance.maintenance.label',
                    'note' => 'maintenance.maintenance.note',
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'checkbox',
                'name' => 'fullmaintenance',
                'attributes' => [
                    'id' => 'fullmaintenance',
                    'class' => 'lotgd toggle'
                ],
                'options' => [
                    'label' => 'maintenance.fullmaintenance.label',
                    'note' => 'maintenance.fullmaintenance.note',
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'textarea',
                'name' => 'maintenancenote',
                'attributes' => [
                    'id' => 'maintenancenote'
                ],
                'options' => [
                    'label' => 'maintenance.maintenancenote'
                ]
            ]
        ],
        [
            'spec' => [
                'type' => 'textarea',
                'name' => 'maintenanceauthor',
                'attributes' => [
                    'id' => 'maintenanceauthor'
                ],
                'options' => [
                    'label' => 'maintenance.maintenanceauthor'
                ]
            ]
        ],
    ]
    ];
