<?php


namespace App\Service;


class Convertisseur
{
    /**
     * Convertit une date Anglaise en francais 2004-05-18 => 18/05/2004
     * @param $jour
     * @return string
     */
    public function jourENtoFR(string $jour): string
    {
        if ($jour == "") {
            return "";
        } else {
            $annee = substr($jour, 0, 4);
            $mois = substr($jour, 5, 2);
            $jour = substr($jour, 8, 2);
            return ($jour . "/" . $mois . "/" . $annee);
        }
    }

    public function decimalToHoursMin(float $decimal): string
    {
        $heure = floor($decimal/60);
        $min = $decimal%60;

        if ($min > 0) {
            return $heure . "h" . $min . "min";
        } else {
            return $heure . "h";
        }


    }
}