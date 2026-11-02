<?php

require_once __DIR__ . "/Conexion.php";

class Usuario
{
    private PDO $pdo;

    public function __construct()
    {
        $this->db = Conexion::obtener();
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $sql = "SELECT u.id_usuario, u.nombres, u.apellidos, u.estado,
                       u.`contraseña` AS clave, r.nombre_rol
                FROM usuario u
                JOIN rol r ON r.id_rol = u.id_rol
                WHERE u.correo = :correo";
        $st = $this->db->prepare($sql);
        $st->execute([':correo' => $correo]);
        $fila = $st->fetch();
        return $fila ?: null;
    }

    public function correoExiste(string $correo): bool
    {
        $st = $this->db->prepare("SELECT COUNT(*) FROM usuario WHERE correo = :correo");
        $st->execute([':correo' => $correo]);
        return (int) $st->fetchColumn() > 0;
    }

    public function crearCliente(string $nombres, string $apellidos, string $correo,
                                 string $telefono, string $claveHash): bool
    {
        $sql = "INSERT INTO usuario (id_rol, nombres, apellidos, correo, telefono, `contraseña`)
                VALUES ((SELECT id_rol FROM rol WHERE nombre_rol = 'Usuario'),
                        :nombres, :apellidos, :correo, :telefono, :clave)";
        $st = $this->db->prepare($sql);
        return $st->execute([
            ':nombres'   => $nombres,
            ':apellidos' => $apellidos,
            ':correo'    => $correo,
            ':telefono'  => $telefono,
            ':clave'     => $claveHash,
        ]);
    }
}
