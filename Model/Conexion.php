<?php

class Conexion
{
    private static ?PDO $pdo = null;

    public static function obtener(): PDO
    {
        if (self::$pdo === null) {
            $host = "localhost";
            $bd   = "stylos_madys";
            $user = "root";
            $pass = "";

            try {
                self::$pdo = new PDO(
                    "mysql:host=$host;dbname=$bd;charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                die("No se puede conectar a la base de datos");
            }
        }
        return self::$pdo;
    }
}