<?php
    function get_blood_messages($rbc, $wbc, $plt, $albumin, $globulin) {
        $messages_list = [];

        if ($rbc == 4) {
            array_push($messages_list, 'Il paziente ha una grave anemia.');
        } else if ($rbc == 5) {
            array_push($messages_list, 'Il paziente ha una grave anemia.');
        } else if ($rbc == 6) {
            array_push($messages_list, 'Il paziente ha una grave anemia.');
        } else if ($rbc == 8) {
            array_push($messages_list, 'Il paziente risulta leggermente disidratato.');
        } else if ($rbc == 9) {
            array_push($messages_list, 'Il paziente è fortemente disidratato, ti consigliamo di aggiungere 5 ml di acqua al cibo per ogni kg di peso.');
        } else if ($rbc == 10) {
            array_push($messages_list, 'Il paziente è fortemente disidratato, ti consigliamo di aggiungere 5 ml di acqua al cibo per ogni kg di peso.');
        }

        if ($wbc < 4) {
            array_push($messages_list, 'Il paziente ha una riduzione dei glubuli bianchi.');
        } else if ($wbc > 14) {
            array_push($messages_list, 'Il paziente ha un aumento anomalo dei globuli bianchi, potrebbe avere una infezione.');
        }

        if ($plt < 150) {
            array_push($messages_list, 'Il paziente ha una riduzione sospetta delle piastrine, potrebbe essere fisiologico in caso di piastrine di grandi dimensioni o potrebbe essere correlata ad un esaurimento delle scorte.');
        } else if ($plt > 350) {
            array_push($messages_list, 'Il paziente ha un aumento anomalo del numero di piastrine, potrebbe essere legato a carenze di ferro o stress.');
        }

        if ($albumin < 2) {
            array_push($messages_list, 'Il paziente ha una grave riduzione delle albumine. Le cause possono essere legate ad un danno renale con perdita di proteine, feci poco formate per lunghi periodi, malattie infettive come la leishmaniosi, diete povere di proteine.');
        } else if ($albumin >= 2 && $albumin <= 2.5) {
            array_push($messages_list, 'Il paziente ha una leggera riduzione delle albumine. Le cause possono essere legate ad un danno renale con perdita di proteine, feci poco formate per lunghi periodi, malattie infettive come la leishmaniosi, diete povere di proteine.');
        } else if ($albumin > 3.5) {
            array_push($messages_list, 'Il paziente è fortemente disidratato, ti consigliamo di aggiungere 5 ml di acqua al cibo per ogni kg di peso e ricontrollare questo valore fra due settimane.');
        }

        if ($globulin >= 3.5 && $globulin <= 4.5) {
            array_push($messages_list, 'Il paziente ha un leggero aumento delle globuline.');
        } else if ($globulin > 4.5) {
            array_push($messages_list, 'Il paziente ha un forte aumento delle globuline.');
        }

        return $messages_list;
    }

    function get_urine_messages($ph, $ps, $wbc, $pu_cu) {
        $messages_list = [];

        if ($ph < 6) {
            array_push($messages_list, "Il paziente ha una forte acidosi metabolica. La dieta dovrebbe normalizzare questo valore. Ti consigliamo di ripetere l'esame fra 2 settimane.");
        } else if ($ph >= 6 && $ph <= 6.5) {
            array_push($messages_list, "Il paziente ha una leggera acidosi metabolica. La dieta dovrebbe normalizzare questo valore. Ti consigliamo di ripetere l'esame fra 2 settimane.");
        } else if ($ph > 8) {
            array_push($messages_list, "Il paziente ha un pH urinario troppo alto. Potrebbe essere legato ad infezioni delle vie urinarie. Ti consigliamo di ripetere l'esame fra 2 settimane.");
        }

        if ($ps < 1.025) {
            array_push($messages_list, 'Il paziente ha le urine eccessivamente diluite.');
        } else if ($ps >= 1.025 && $ps <= 1.030) {
            array_push($messages_list, 'Il paziente ha le urine diluite.');
        } else if ($ps > 1.035) {
            array_push($messages_list, 'Il paziente è fortemente disidratato.');
        }

        if ($wbc == 1) {
            array_push($messages_list, 'Il paziente ha una infezione o una infiammazione delle vie urinarie.');
        }

        if ($pu_cu > 0.5) {
            array_push($messages_list, 'Il paziente ha una perdita anomala di proteine con le urine.');
        }

        return $messages_list;
    }
?>