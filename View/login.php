<?php
require_once __DIR__ . '/../Controller/AuthController.php';
require_once __DIR__ . '/../Controller/Ayudas.php';

// Si ya inició sesión, no tiene sentido mostrar el login
if (Sesion::usuario() !== null) {
    header('Location: index.html');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = (new AuthController())->iniciarSesion($_POST);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Iniciar sesión · Stylos Madys</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=DM+Sans:wght@400;500;600&family=Playfair+Display:ital,wght@1,500;1,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<main class="auth">
<div class="brand"><div class="logo big"><span class="sm">SM</span><span><span class="nm">STYLOS MADYS</span><small>SALÓN DE BELLEZA CAPILAR</small></span></div><div class="tagline">Tu cabello, nuestra mejor versión</div></div>
<div class="right ph-img" style="background-image:url(../img/fondo-salon.jpg)">
<form class="box" method="post" action="login.php">
<h2><i>☺</i>Iniciar sesión</h2>
<p>Ingresa a tu cuenta para agendar tus citas</p>
<?php if (isset($_GET['registro'])): ?>
<div class="msg ok">Cuenta creada. Ya puedes iniciar sesión.</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="msg err"><?= e($error) ?></div>
<?php endif; ?>
<label class="field"><i>✉</i><input type="email" name="correo" value="<?= e($_POST['correo'] ?? '') ?>" placeholder="Correo electrónico" aria-label="Correo electrónico" autocomplete="email" required></label><label class="field"><i>🔒︎</i><input type="password" name="clave" placeholder="Contraseña" aria-label="Contraseña" autocomplete="current-password" required><i>◉</i></label>
<div class="opts"><label><input type="checkbox"> Recordar mi cuenta</label><a href="#">¿Olvidaste tu contraseña?</a></div>
<button class="btn block" type="submit">Iniciar sesión →</button>
<div class="foot"><span>¿No tienes una cuenta?</span><a class="lk" href="registro.php">Regístrate</a></div>
</form></div></main>
</body>
</html>