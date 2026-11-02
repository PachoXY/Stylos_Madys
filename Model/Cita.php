<?php

require_once __DIR__ . '/Conexion.php';

class Cita
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Conexion::obtener();
    }

    public function listarPendientes(): array
    {
        $sql = "SELECT * FROM v_agenda
                WHERE estado IN ('Pendiente','Confirmada')
                ORDER BY fecha_cita, hora_inicio";
        return $this->db->query($sql)->fetchAll();
    }

    public function listarRealizadas(): array
    {
        $sql = "SELECT * FROM v_agenda
                WHERE estado = 'Finalizada'
                ORDER BY fecha_cita DESC, hora_inicio DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function contarPendientes(): int
    {
        $sql = "SELECT COUNT(*) FROM cita WHERE estado IN ('Pendiente','Confirmada')";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    public function contarRealizadas(): int
    {
        $sql = "SELECT COUNT(*) FROM cita WHERE estado = 'Finalizada'";
        return (int) $this->db->query($sql)->fetchColumn();
    }
}