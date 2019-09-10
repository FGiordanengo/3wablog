<?php

namespace App\Service;

class Calculator {

    public function getTotal($totalHt, $taux = 20) {
        return $totalHt + ($totalHt * $taux / 100);
    }

}
