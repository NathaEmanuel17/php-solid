<?php
return [
    'name' => $_ENV['APP_NAME'] ?? 'Minha App',
    'env'  => $_ENV['APP_ENV']  ?? 'local',
    'tz'   => $_ENV['APP_TZ']   ?? 'America/Sao_Paulo',
];
