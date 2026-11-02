<?php
require_once __DIR__ . '/../Controller/AuthController.php';
require_once __DIR__ . '/../Controller/Ayudas.php';

$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = (new AuthController())->registrar($_POST);
    if (!$errores) {
        header('Location: login.php?registro=ok');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registro · Stylos Madys</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=DM+Sans:wght@400;500;600&family=Playfair+Display:ital,wght@1,500;1,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<main class="auth">
<div class="brand"><div class="logo big"><span class="sm">SM</span><span><span class="nm">STYLOS MADYS</span><small>SALÓN DE BELLEZA CAPILAR</small></span></div><div class="tagline">Tu cabello, nuestra mejor versión</div></div>
<div class="right ph-img" style="background-image:url(../img/fondo-salon.jpg)">
<form class="box" action="login.php">
<h2><i>☺</i>Registrar</h2>
<p>Crea tu cuenta para agendar tus citas</p>
<label class="field"><i>☺</i><input type="text" placeholder="Nombre completo" aria-label="Nombre completo"></label><label class="field"><i>✉</i><input type="email" placeholder="Correo electrónico" aria-label="Correo electrónico"></label><label class="field"><i>☎</i><input type="tel" placeholder="Número de teléfono" aria-label="Número de teléfono"></label><label class="field"><i>🔒︎</i><input type="password" placeholder="Contraseña" aria-label="Contraseña"><i>◉</i></label><label class="field"><i>🔒︎</i><input type="password" placeholder="Confirmar contraseña" aria-label="Confirmar contraseña"><i>◉</i></label>
<button class="btn block" type="submit">Registrarse →</button>
<div class="foot"><span>¿Ya tienes una cuenta?</span><a class="lk" href="login.php">Iniciar sesión</a></div>
</form></div></main>
</body>
</html>
