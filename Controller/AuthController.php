<?php
require_once __DIR__ . '/../Model/Usuario.php';
require_once __DIR__ . '/Sesion.php';

class AuthController
{
    private Usuario $usuario;

    public function __construct()
    {
        $this->usuario = new Usuario();
    }


    public function iniciarSesion(array $post): ?string
    {
        $correo = trim($post['correo'] ?? '');
        $clave  = $post['clave'] ?? '';

        if ($correo === '' || $clave === '') {
            return 'Ingresa tu correo y tu contraseña.';
        }

        $u = $this->usuario->buscarPorCorreo($correo);

        // Mismo mensaje si el correo no existe o la clave está mal (no damos pistas)
        if ($u === null || !password_verify($clave, $u['clave'])) {
            return 'Correo o contraseña incorrectos.';
        }
        if ($u['estado'] !== 'Activo') {
            return 'Tu cuenta está inactiva. Comunícate con el salón.';
        }

        Sesion::guardarUsuario($u);

        $destino = ($u['nombre_rol'] === 'Administrador') ? 'admin-inicio.html' : 'index.html';
        header('Location: ' . $destino);
        exit;
    }

    public function registrar(array $post): array
    {
        $nombre   = trim(preg_replace('/\s+/', ' ', $post['nombre'] ?? ''));
        $correo   = trim($post['correo'] ?? '');
        $telefono = preg_replace('/\s+/', '', $post['telefono'] ?? '');
        $clave    = $post['clave'] ?? '';
        $clave2   = $post['clave2'] ?? '';
        $errores  = [];

        $palabras = ($nombre === '') ? [] : explode(' ', $nombre);
        if (count($palabras) < 2 || !preg_match('/^[\p{L}\s\'-]+$/u', $nombre)) {
            $errores[] = 'Escribe tu nombre y apellido (solo letras).';
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || mb_strlen($correo) > 150) {
            $errores[] = 'El correo electrónico no es válido.';
        }
        if (!preg_match('/^[0-9]{7,10}$/', $telefono)) {
            $errores[] = 'El teléfono debe tener entre 7 y 10 números.';
        }
        if (strlen($clave) < 8 || !preg_match('/[A-Z]/', $clave)
            || !preg_match('/[a-z]/', $clave) || !preg_match('/[0-9]/', $clave)) {
            $errores[] = 'La contraseña debe tener mínimo 8 caracteres, con mayúscula, minúscula y número.';
        }
        if ($clave !== $clave2) {
            $errores[] = 'Las contraseñas no coinciden.';
        }
        if (!$errores && $this->usuario->correoExiste($correo)) {
            $errores[] = 'Ya existe una cuenta con ese correo.';
        }
        if ($errores) {
            return $errores;
        }

        $corte     = max(1, intdiv(count($palabras), 2));
        $nombres   = implode(' ', array_slice($palabras, 0, $corte));
        $apellidos = implode(' ', array_slice($palabras, $corte));

        $this->usuario->crearCliente($nombres, $apellidos, $correo, $telefono,
                                     password_hash($clave, PASSWORD_DEFAULT));
        return [];
    }
}