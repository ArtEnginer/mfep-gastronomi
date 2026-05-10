<?php
require_once 'includes/auth.php';

adminLogout();
header('Location: login.php');
exit;
