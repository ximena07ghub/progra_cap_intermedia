<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/app.php';

header('Location: ' . url('login.php'), true, 302);
exit;
