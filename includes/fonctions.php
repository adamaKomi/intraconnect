<?php

function calculateDateDifference($dateString1, $dateString2)
{
    // Convertir les chaînes de caractères en objets DateTime
    $date1 = new DateTime($dateString1);
    $date2 = new DateTime($dateString2);

    // Convertir les dates en secondes
    $timestamp1 = $date1->getTimestamp();
    $timestamp2 = $date2->getTimestamp();

    // Calculer la différence en secondes
    $diffInSeconds = abs($timestamp2 - $timestamp1);

    // Calculer les années, mois, jours, heures, minutes et secondes à partir de la différence en secondes
    $years = floor($diffInSeconds / (365 * 24 * 60 * 60));
    $months = floor(($diffInSeconds - $years * 365 * 24 * 60 * 60) / (30 * 24 * 60 * 60));
    $days = floor(($diffInSeconds - $years * 365 * 24 * 60 * 60 - $months * 30 * 24 * 60 * 60) / (24 * 60 * 60));
    $hours = floor(($diffInSeconds - $years * 365 * 24 * 60 * 60 - $months * 30 * 24 * 60 * 60 - $days * 24 * 60 * 60) / (60 * 60));
    $minutes = floor(($diffInSeconds - $years * 365 * 24 * 60 * 60 - $months * 30 * 24 * 60 * 60 - $days * 24 * 60 * 60 - $hours * 60 * 60) / 60);
    $seconds = $diffInSeconds - $years * 365 * 24 * 60 * 60 - $months * 30 * 24 * 60 * 60 - $days * 24 * 60 * 60 - $hours * 60 * 60 - $minutes * 60;

    // Initialiser la chaîne de résultat
    $result = '';

    // Ajouter les années à la chaîne de résultat
    if ($years > 0) {
        $result .= $years . " Year" . ($years > 1 ? "s" : "") . " ";
    }

    // Ajouter les mois à la chaîne de résultat
    elseif ($months > 0) {
        $result .= $months . " Month ";
    }

    // Ajouter les jours à la chaîne de résultat
    elseif ($days > 0) {
        $result .= $days . " Day" . ($days > 1 ? "s" : "") . " ";
    }

    // Ajouter les heures à la chaîne de résultat
    elseif ($hours > 0) {
        $result .= $hours . " Hour" . ($hours > 1 ? "s" : "") . " ";
    }

    // Ajouter les minutes à la chaîne de résultat
    elseif ($minutes > 0) {
        $result .= $minutes . " Minute" . ($minutes > 1 ? "s" : "") . " ";
    }

    // Ajouter les secondes à la chaîne de résultat
    elseif ($seconds > 0) {
        $result .= $seconds . " Second" . ($seconds > 1 ? "s" : "") . " ";
    }

    // Ajouter "Ago" à la fin de la chaîne de résultat
    $result .= "Ago";

    // Retourner la chaîne de résultat
    return $result;
}


function dateAction($dateString)
{
    //recuperer la date de publication
    $datePublication = new DateTime($dateString);
   
    // Obtenir l'heure actuelle avec le fuseau horaire du Maroc
    $maintenant = new DateTime('now', new DateTimeZone('Africa/Casablanca'));

    //calculer la difference et gerer l'affichage
    $temps = calculateDateDifference($datePublication->format('Y-m-d H:i:s'), $maintenant->format('Y-m-d H:i:s'));

    return  $temps;
}


function pourcentage($premier,$deuxieme){
    if ($deuxieme) {
        $info['pourcentage'] = intval((abs($deuxieme - $premier) / $deuxieme * 100)) . '%';
        $info['couleur'] = ($deuxieme > $premier) ? 'danger' : (($deuxieme < $premier) ? 'success' : 'primary');
      } else {
        $info['pourcentage'] = '+' . $premier;
        $info['couleur'] = $deuxieme == $premier ? 'primary' : 'success';
      }
      return $info;
}
