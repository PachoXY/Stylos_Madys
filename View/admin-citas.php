<?php
require_once __DIR__ . '/../Controller/Sesion.php';
require_once __DIR__ . '/../Controller/Ayudas.php';
require_once __DIR__ . '/../Controller/CitaController.php';

Sesion::exigirRol('Administrador');

$controlador = new CitaController();
$datos = $controlador->index();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Citas · Stylos Madys</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=DM+Sans:wght@400;500;600&family=Playfair+Display:ital,wght@1,500;1,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="adm"><aside class="side"><a class="logo" href="index.html"><span class="sm">SM</span><span><span class="nm">STYLOS <b>MADYS</b></span><small>SALÓN DE BELLEZA CAPILAR</small></span></a><a href="admin-inicio.html"><span>⌂</span>Inicio</a><a href="admin-citas.php" class="on"><span>▦</span>Citas</a><a href="admin-clientes.html"><span>☺</span>Clientes</a><a href="admin-servicios.html"><span>✂</span>Servicios</a><a href="admin-reportes.html"><span>▮</span>Reportes</a><a class="out" href="logout.php"><span>⇥</span>Cerrar sesión</a></aside>
<main class="main"><div class="bar"><div><b>Hola, Administrador ✦</b><small>Aquí tienes un resumen de tus citas</small></div><div class="me"><span class="bell">♪<sup>3</sup></span><span class="ico">☺</span>Admin ▾</div></div>
<div class="ban ph-img" style="background-image:url(../img/banner-admin.jpg)"><h2>Citas</h2><p>STYLOS MADYS</p><small>Tu belleza, nuestra prioridad</small></div>


<div class="bx" style="margin-bottom:18px"><h3><span class="ico">▦</span><span><span class="t">Citas por hacer</span><small>Total de citas pendientes: <?= $datos['totalPendientes'] ?></small></span><span class="tools"><input placeholder="Buscar cliente…" aria-label="Buscar cliente" data-filtro="tabla-pendientes"><span class="btn sm">+ Nueva cita</span></span></h3>
<div class="tb"><table id="tabla-pendientes"><tr><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Servicio</th><th>Estado</th><th>Acciones</th></tr>
<?php if (empty($datos['pendientes'])): ?>
<tr><td colspan="6">No hay citas pendientes.</td></tr>
<?php endif; ?>
<?php foreach ($datos['pendientes'] as $c): ?>
<tr><td><?= e($c['fecha']) ?></td><td><?= e($c['hora']) ?></td><td><div class="who"><div><?= e($c['cliente']) ?><small><?= e($c['telefono']) ?></small></div></div></td><td><?= e($c['nombre_servicio']) ?></td><td><span class="chip <?= e($c['claseEstado']) ?>"><?= e($c['estado']) ?></span></td><td><div class="act"><span>✎</span><span>🗑︎</span></div></td></tr>
<?php endforeach; ?>
</table></div></div>


<div class="bx"><h3><span class="ico">✓</span><span><span class="t">Citas realizadas</span><small>Total de citas completadas: <?= $datos['totalRealizadas'] ?></small></span><span class="tools"><input placeholder="Buscar cliente…" aria-label="Buscar cliente" data-filtro="tabla-realizadas"></span></h3>
<div class="tb"><table id="tabla-realizadas"><tr><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Servicio</th><th>Estado</th></tr>
<?php if (empty($datos['realizadas'])): ?>
<tr><td colspan="5">Aún no hay citas realizadas.</td></tr>
<?php endif; ?>
<?php foreach ($datos['realizadas'] as $c): ?>
<tr><td><?= e($c['fecha']) ?></td><td><?= e($c['hora']) ?></td><td><div class="who"><div><?= e($c['cliente']) ?><small><?= e($c['telefono']) ?></small></div></div></td><td><?= e($c['nombre_servicio']) ?></td><td><span class="chip <?= e($c['claseEstado']) ?>"><?= e($c['estado']) ?></span></td></tr>
<?php endforeach; ?>
</table></div></div></main></div>

<script>

document.querySelectorAll('input[data-filtro]').forEach(function (input) {
  input.addEventListener('input', function () {
    var texto = input.value.toLowerCase();
    var filas = document.querySelectorAll('#' + input.dataset.filtro + ' tr');
    filas.forEach(function (fila, i) {
      if (i === 0) return; // encabezado
      fila.style.display = fila.textContent.toLowerCase().includes(texto) ? '' : 'none';
    });
  });
});
</script>
</body>
</html>