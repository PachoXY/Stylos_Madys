<?php
class Sesion
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

 
    public static function guardarUsuario(array $u): void
    {
        self::iniciar();
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'     => (int) $u['id_usuario'],
            'nombre' => $u['nombres'] . ' ' . $u['apellidos'],
            'rol'    => $u['nombre_rol'],
        ];
    }


    public static function usuario(): ?array
    {
        self::iniciar();
        return $_SESSION['usuario'] ?? null;
    }


    public static function exigirRol(string ...$roles): void
    {
        $u = self::usuario();
        if ($u === null) {
            header('Location: login.php');
            exit;
        }
        if (!in_array($u['rol'], $roles, true)) {
            header('Location: index.html');
            exit;
        }
    }

    public static function cerrar(): void
    {
        self::iniciar();
        $_SESSION = [];
        session_destroy();
    }
}