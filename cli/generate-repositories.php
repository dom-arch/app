<?php
require_once 'cli.php';

(function() {
    $config = Lib\Config::global()
        ->get('context')
            ->get('repositories');


    $filters = [];

    $params = [
        'orm:generate-repositories'
    ];

    if (count($_SERVER['argv']) > 1) {
        $names = array_slice($_SERVER['argv'], 1);

        foreach ($names as $name) {
            $filters[] = '--filter ' . $name;
        }

        $params[] = implode(' ', $filters);
    }

    $params[] = '--';
    $params[] = $config->get('directory');

    Indoctrinated\Db::run(implode(' ', $params));
})();
