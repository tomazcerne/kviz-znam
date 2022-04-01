<?php
    session_start();
    if(isset($_SESSION["id_uporabnika"]) && isset($_POST["id_vprasanja"]) && isset($_POST["id_rezultata"]) && isset($_POST["izbira"]) && isset($_POST["pravilno"])){
        require("../../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: ../odjava.php");
            exit();
        }
        else{
            $zabelezi = $baza->prepare("INSERT INTO odgovori(id_uporabnika, id_vprasanja, odgovor, datum_odgovora) VALUES (?,?,?,?)");
            $d = date("Y-m-d H:i:s");
            $zabelezi->bind_param("iiss", $_SESSION["id_uporabnika"], $_POST["id_vprasanja"], $_POST["izbira"], $d);
            $zabelezi->execute();

            $popravi_datum = $baza->prepare("UPDATE rezultati SET datum_rezultata = ? WHERE id_rezultata = ?");
            $popravi_datum->bind_param("si", $d, $_POST["id_rezultata"]);
            $popravi_datum->execute();

            if($_POST["pravilno"]=="true"){
                $popravi_rezultat = $baza->prepare("UPDATE rezultati SET st_pravilnih = st_pravilnih + 1 WHERE id_rezultata = ?");
                $popravi_rezultat->bind_param("i", $_POST["id_rezultata"]);
                $popravi_rezultat->execute();
            }
            $baza->close();
            
        }
    }
?>