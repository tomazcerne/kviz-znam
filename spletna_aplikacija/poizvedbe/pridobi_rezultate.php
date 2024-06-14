<?php
    if(isset($_POST["id_vprasanja"])){

        $st = array("A"=>0,"B"=>0,"C"=>0,"D"=>0);

        require("../podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: ../odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT COUNT(id_odgovora) AS st, odgovor FROM odgovori WHERE id_vprasanja = ? GROUP BY odgovor");
            $pridobi_podatke->bind_param("i", $_POST["id_vprasanja"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    while($vrstica = $rezultat->fetch_assoc()){
                        $st[$vrstica["odgovor"]] = $vrstica["st"];
                    }
                }
            }
            $baza->close();
        }
    echo $st["A"] . "," . $st["B"] . "," . $st["C"] . "," . $st["D"];
    }
?>