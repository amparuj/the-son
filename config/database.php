<?php

return [
  'default' => env('DB_CONNECTION', 'mysql'),

  'connections' => [
    'sqlite' => [
      'driver' => 'sqlite',
      'database' => env('DB_DATABASE', database_path('database.sqlite')),
      'prefix' => '',
      'foreign_key_constraints' => true,
    ],
    'mysql' => [
      'driver' => 'mysql',
      'host' => env('DB_HOST', '127.0.0.1'),
      'port' => env('DB_PORT', '3306'),
      'database' => env('DB_DATABASE', 'laravel'),
      'username' => env('DB_USERNAME', 'root'),
      'password' => env('DB_PASSWORD', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'prefix_indexes' => true,
      'strict' => true,
    ],
    'pgsql' => [
      'driver' => 'pgsql',
      'host' => env('DB_HOST', '127.0.0.1'),
      'port' => env('DB_PORT', '5432'),
      'database' => env('DB_DATABASE', 'laravel'),
      'username' => env('DB_USERNAME', 'postgres'),
      'password' => env('DB_PASSWORD', ''),
      'charset' => 'utf8',
      'prefix' => '',
      'prefix_indexes' => true,
      'search_path' => 'public',
      'sslmode' => 'prefer',
    ],
  ],

  'migrations' => 'migrations',
];
