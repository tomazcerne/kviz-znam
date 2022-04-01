<?php
    $vrni = false;
    if(isset($_POST["id_vprasanja"])){
        require("../../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: ../odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT pravilen FROM vprasanja WHERE id_vprasanja = ?");
            $pridobi_podatke->bind_param("i", $_POST["id_vprasanja"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    $vrni = $vrstica["pravilen"];
                }
            }
            $baza->close();
        }
    }
    echo $vrni;
?>