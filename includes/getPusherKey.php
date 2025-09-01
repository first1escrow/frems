<?php
require_once dirname(__DIR__) . '/.env.php';

exit($env['pusher']['key']);
