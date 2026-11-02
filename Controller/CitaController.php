<?php

require_once __DIR__ . "/../Model/Cita.php";

class CitaController{
     
    public function index(): array
    {
        $cita = new Cita();

        return [

         "pendientes" => array_map([$this, "preparar"], $cita->listarPendientes()),
         "realizadas" => array_map([$this,"preparar"], $cita->listarRealizadas()),
         "totalPendientes" => $cita->contarPendientes(),
         "totalRealizadas" => $cita->contarRealizadas(),
        ];
    }

    public function preparar(array $c): array
    {
        $c["fecha"] = $this->formatearFecha($c["fecha_cita"]);
        $c["hora"] = $this->formatearHora($c["hora_inicio"]);
        $c["claseEstado"] = $this->claseEstado($c["estado"]);
        return $c;
    }

    private function formatearFecha(string $fecha): string
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                  'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $t = strtotime($fecha);
        return date('d', $t) . ' ' . $meses[(int) date('n', $t) - 1] . '. ' . date('Y', $t);
    }

   
    private function formatearHora(string $hora): string
    {
        $t = strtotime($hora);
        return date('h:i', $t) . (date('A', $t) === 'AM' ? ' a. m.' : ' p. m.');
    }

   
    private function claseEstado(string $estado): string
    {
        return match ($estado) {
            'Confirmada', 'Finalizada' => 'ok',
            'Cancelada'                => 'bad',
            default                    => '',
        };
    }

}