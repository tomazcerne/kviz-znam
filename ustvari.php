<?php
    session_start();

    if(!isset($_SESSION["id_uporabnika"])){
        header("Location: prijava");
        exit();
    }

    unset($_SESSION["ust_napake"]);

    $podatki["naslov_kviza"] = trim($_POST["naslov_kviza"]);
    $podatki["opis"] = trim($_POST["opis"]);
    $podatki["geslo_kviza"] = trim($_POST["geslo_kviza"]);
 
    foreach($podatki as $input => $vnos){
        preveri($input, $vnos);
    }

    function preveri($input, $vnos){ 
        $max["naslov_kviza"] = 50;
        $max["opis"] = 300;
        $max["geslo_kviza"] = 30;

        if(empty($vnos) && $input == "naslov_kviza"){
            $_SESSION["ust_napake"][$input] = "Obvezno izpolnite to polje";
        }
        else if(mb_strlen($vnos, "UTF-8") > $max[$input]){
            $_SESSION["ust_napake"][$input] = "Vnešeni podatki naj ne presegajo " . $max[$input] . " znakov";
        }
    }

    if(!isset($_SESSION["ust_napake"])){
        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            $_SESSION["ust_napake"]["splosna"] = "Povezava žal ni bila uspešna. ";
        }
        else{
            $preveri_naslov = $baza->prepare("SELECT * FROM kvizi WHERE id_uporabnika = ? AND naslov_kviza = ?");
            $preveri_naslov->bind_param("is", $_SESSION["id_uporabnika"], $podatki["naslov_kviza"]);
            if($preveri_naslov->execute()){
                if($preveri_naslov->get_result()->num_rows == 0){
                    $dodaj_kviz = $baza->prepare("INSERT INTO kvizi (id_uporabnika, naslov_kviza, opis, geslo_kviza, datum_kviza, enkrat) VALUES (?,?,?,?,?,?)");
                    $e = 0;
                    if(isset($_POST["enkrat"])){
                        $e = 1;
                    }
                    $g = md5($podatki["geslo_kviza"]);
                    $d = date("Y-m-d H:i:s");
                    $dodaj_kviz->bind_param("issssi", $_SESSION["id_uporabnika"], $podatki["naslov_kviza"], $podatki["opis"], $g, $d, $e);
                    if(!$dodaj_kviz->execute()){
                        $_SESSION["ust_napake"]["splosna"] = "Napaka!. Poizkusite ponovno";
                    }

                    $pridobi_podatke = $baza->prepare("SELECT id_kviza FROM kvizi WHERE datum_kviza = ?");
                    $pridobi_podatke->bind_param("s", $d);
                    if($pridobi_podatke->execute()){
                        $rezultat = $pridobi_podatke->get_result();
                        if($rezultat->num_rows > 0){
                            $vrstica = $rezultat->fetch_assoc();
                            $id_kviza = $vrstica["id_kviza"];
                        }
                    }
                }
                else{
                    $_SESSION["ust_napake"]["naslov_kviza"] = "Kviz s tem naslovom že obstaja";
                }
            }
            else{
                $_SESSION["ust_napake"]["splosna"] = "Napaka! Poizkusite ponovno";
            }
            $baza->close();
        }
    }
    
    if(isset($_SESSION["ust_napake"])){
        $_SESSION["ust_input"]["naslov_kviza"] = $podatki["naslov_kviza"];
        $_SESSION["ust_input"]["opis"] = $podatki["opis"];
        header("location: ustvari_kviz.php");
        exit();
    }
    else{
        header("location: uredi_kviz.php?id_kviza=". $id_kviza);
        exit();
    }

?>