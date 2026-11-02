<?php
require_once __DIR__ . '/../Controller/Sesion.php';

Sesion::cerrar();
header('Location: login.php');
exit;