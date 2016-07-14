<?php
require_once 'cli.php';

(function() {
    $config = Lib\Config::global()
        ->get('context')
            ->get('entities');

    $filters = [];

    $params = [
        'orm:convert-mapping',
        '--from-database',
        '--extend=' . $config->get('class')
    ];

    if (count($_SERVER['argv']) > 1) {
        $names = array_slice($_SERVER['argv'], 1);

        foreach ($names as $name) {
            $filters[] = '--filter ' . $name;
        }

        $params[] = implode(' ', $filters);
    }

    $params[] = $config->get('type');
    $params[] = $config->get('directory');

    Indoctrinated\Db::run(implode(' ', $params));
})();
