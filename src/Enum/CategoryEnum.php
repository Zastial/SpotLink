<?php

namespace App\Enum;

enum CategoryEnum: string {
    case SPORT = 'Sport';
    case CONCERT = 'Concert';
    case CONFERENCE = 'Conférence';
    case EXPOSITION = 'Exposition';
    case FESTIVAL = 'Festival';
    case THEATRE = 'Théâtre';
    case CINEMA = 'Cinéma';
    case ATELIER = 'Atelier';
    case MARCHE = 'Marché';
    case AUTRE = 'Autre';

    public function getIcon(): string {
        return match($this) {
            self::SPORT => 'fas fa-running',
            self::CONCERT => 'fas fa-microphone-alt',
            self::CONFERENCE => 'fas fa-chalkboard-teacher',
            self::EXPOSITION => 'fas fa-paint-brush',
            self::FESTIVAL => 'fas fa-music',
            self::THEATRE => 'fas fa-theater-masks',
            self::CINEMA => 'fas fa-film',
            self::ATELIER => 'fas fa-tools',
            self::MARCHE => 'fas fa-shopping-cart',
            self::AUTRE => 'fas fa-question',
        };
    }
}
