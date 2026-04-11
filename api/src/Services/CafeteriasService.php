<?php

declare(strict_types=1);

namespace Occu\Api\Services;

use Occu\Api\Repositories\CafeteriasRepository;
use Occu\Api\Support\Clock;

final class CafeteriasService
{
    private CafeteriasRepository $cafeteriasRepo;

    public function __construct(CafeteriasRepository $cafeteriasRepo)
    {
        $this->cafeteriasRepo = $cafeteriasRepo;
    }

    /**
     * Calcula si una cafetería está abierta ahora y devuelve el horario del día actual
     * Basado en GeneralController::isOpen del legacy
     * 
     * @return array{0: bool, 1: string} [isOpen, todayScheduleLabel]
     */
    public function calculateIsOpen(int $cafeteriaId): array
    {
        $zonaHoraria = $this->cafeteriasRepo->getZonaHoraria($cafeteriaId) ?: 'America/Tijuana';
        $now = Clock::nowImmutable($zonaHoraria);
        $currentTime = $now->format('H:i');
        $currentDayEnglish = $now->format('l');
        $currentDay = $this->translateDay($currentDayEnglish);

        $horariosSimples = $this->cafeteriasRepo->getHorariosSimples($cafeteriaId);
        $horariosDetallados = $this->cafeteriasRepo->getHorariosDetallados($cafeteriaId);

        // Si tiene horarios simples
        if ($horariosSimples && 
            !empty($horariosSimples['horario_apertura']) && 
            !empty($horariosSimples['horario_cierre'])) {
            $isOpen = ($currentTime >= $horariosSimples['horario_apertura'] && 
                       $currentTime <= $horariosSimples['horario_cierre']);
            $horarioDiaActual = $horariosSimples['horario_apertura'] . ' - ' . $horariosSimples['horario_cierre'];
            return [$isOpen, $horarioDiaActual];
        }

        // Si tiene horarios detallados
        if (!empty($horariosDetallados) && isset($horariosDetallados[$currentDay])) {
            $horarioDia = $horariosDetallados[$currentDay];

            if ($horarioDia['cerrado'] === 'SI') {
                return [false, 'Cerrado los ' . $currentDay];
            }

            if (empty($horarioDia['apertura']) || empty($horarioDia['cierre'])) {
                return [false, 'Horario no disponible'];
            }

            $isOpen = ($currentTime >= $horarioDia['apertura'] && $currentTime <= $horarioDia['cierre']);
            $horarioDiaActual = $horarioDia['apertura'] . ' - ' . $horarioDia['cierre'];
            return [$isOpen, $horarioDiaActual];
        }

        return [false, 'Horario no disponible'];
    }

    private function translateDay(string $dayEnglish): string
    {
        $days = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];
        return $days[$dayEnglish] ?? $dayEnglish;
    }
}
