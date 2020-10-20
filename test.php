<?php

require_once "email_check.class.php";

$hash = get_option('ec_hash');
$checker = new emailCheck($hash);

$result = $checker->check('');
