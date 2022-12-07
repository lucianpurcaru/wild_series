<?php

namespace App\Service;

use App\Entity\Program;

class ProgramDuration
{
    public function calculate(Program $program, string $type) {
        $duration = 0; 
        foreach ($program->getSeasons() as $season) {
            foreach ($season->getEpisodes() as $episode) {
                $duration += $episode->getDuration();
            }
        }
        $duration = $duration*60;
        return $this->timeDiffMinutes($duration, $type);
    }

    public function timeDiffMinutes(int $seconds, string $type) {
        $timeDiffFrom = new \DateTime('@0');
        $timeDiffTo = new \DateTime("@$seconds");
        if ($type === 'minutes') {
            return $timeDiffFrom->diff($timeDiffTo)->format('%a jours, %h heures et %i minutes');
        } elseif ($type === 'secondes') {
            return $timeDiffFrom->diff($timeDiffTo)->format('%a jours, %h heures, %i minutes et %s secondes');
        } else {
            throw new \Error;
        }
    }
}