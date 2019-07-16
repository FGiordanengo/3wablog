<?php

namespace App\Service;

class Calculator
{
    public function getTotal($totalHT, $taux = 20)
    {
        return $totalHT + ($totalHT * $taux / 100);
    }
}
