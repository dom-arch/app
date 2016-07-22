<?php
require_once 'cli.php';

echo 'Creating a new user (one user required as minimum)';

$fields = [
    'account' => 'dev',
    'type' => 'dev',
    'email' => 'dev@domain.tld',
    'password' => 'password',
    'locale' => 'en'
];

$length = count($fields);
$keys = array_keys($fields);

for ($iterator = 0; $iterator < $length; $iterator += 1) {
    $key = $keys[$iterator];

    echo 'Enter the user ' . $key . ' [' . $fields[$key] . '] : ';

    $input = trim(fgets(STDIN));

    $fields[$key] = empty($input)
        ? $fields[$key]
        : $input;
}

$fields['password'] = password_hash($fields['password'], PASSWORD_DEFAULT);

Users::fromArray($fields)->save();

echo 'done';

exit;