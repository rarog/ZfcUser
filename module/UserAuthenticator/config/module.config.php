<?php

use Laminas\Router\Http\Literal;

return [
    'view_manager' => [
        'template_path_stack' => [
            'zfcuser' => __DIR__ . '/../view',
        ],
    ],

    'router' => [
        'routes' => [
            'zfcuser' => [
                'type' => Literal::class,
                'priority' => 1000,
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'controller' => 'zfcuser',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => 'zfcuser',
                                'action'     => 'login',
                            ],
                        ],
                    ],
                    'authenticate' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/authenticate',
                            'defaults' => [
                                'controller' => 'zfcuser',
                                'action'     => 'authenticate',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => 'zfcuser',
                                'action'     => 'logout',
                            ],
                        ],
                    ],
                    'register' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/register',
                            'defaults' => [
                                'controller' => 'zfcuser',
                                'action'     => 'register',
                            ],
                        ],
                    ],
                    'changepassword' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/change-password',
                            'defaults' => [
                                'controller' => 'zfcuser',
                                'action'     => 'changepassword',
                            ],
                        ],
                    ],
                    'changeemail' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/change-email',
                            'defaults' => [
                                'controller' => 'zfcuser',
                                'action' => 'changeemail',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
