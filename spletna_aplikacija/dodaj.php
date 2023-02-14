<?php
    session_start();

    if(!isset($_SESSION["id_uporabnika"])){
        header("Location: prijava");
        exit();
    }
    else if(isset($_GET["id_kviza"])){
        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT id_uporabnika FROM kvizi WHERE id_kviza = ?");
            $pridobi_podatke->bind_param("i", $_GET["id_kviza"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    if($vrstica["id_uporabnika"] != $_SESSION["id_uporabnika"]){
                        header("Location: ../");
                        exit();
                    }
                }
                else{
                    header("Location: ../");
                    exit();
                }
            }
            else{
                header("Location: ../");
                exit();
            }
            $baza->close();
        }
        
    }
    else{
        header("Location: ../");
        exit();
    }

    unset($_SESSION["dod_napake"]);

    $podatki["vprasanje"] = trim($_POST["vprasanje"]);
    $podatki["odgovor_A"] = trim($_POST["odgovor_A"]);
    $podatki["odgovor_B"] = trim($_POST["odgovor_B"]);
    $podatki["odgovor_C"] = trim($_POST["odgovor_C"]);
    $podatki["odgovor_D"] = trim($_POST["odgovor_D"]);
 
    foreach($podatki as $input => $vnos){
        preveri($input, $vnos);
    }

    function preveri($input, $vnos){ 
        $max["vprasanje"] = 200;
        $max["odgovor_A"] = 50;
        $max["odgovor_B"] = 50;
        $max["odgovor_C"] = 50;
        $max["odgovor_D"] = 50;

        if($vnos==""){
            $_SESSION["dod_napake"][$input] = "Obvezno izpolnite to polje";
        }
        else if(mb_strlen($vnos, "UTF-8") > $max[$input]){
            $_SESSION["dod_napake"][$input] = "Vnešeni podatki naj ne presegajo " . $max[$input] . " znakov";
        }
    }

    if(empty($_POST["pravilen"])){
        $_SESSION["dod_napake"]["splosna"] = "Označite, kateri izmed odgovorov je pravilen";
    }

    if(!isset($_SESSION["dod_napake"])){
        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            $_SESSION["dod_napake"]["splosna"] = "Povezava žal ni bila uspešna. ";
        }
        else{
            $dodaj_vprasanje = $baza->prepare("INSERT INTO vprasanja (id_kviza, vprasanje, odgovor_A, odgovor_B, odgovor_C, odgovor_D, pravilen, datum_vprasanja) VALUES (?,?,?,?,?,?,?,?)");
            $d = date("Y-m-d H:i:s");
            $dodaj_vprasanje->bind_param("isssssss", $_GET["id_kviza"], $podatki["vprasanje"], $podatki["odgovor_A"], $podatki["odgovor_B"], $podatki["odgovor_C"], $podatki["odgovor_D"], $_POST["pravilen"], $d);
            if(!$dodaj_vprasanje->execute()){
                $_SESSION["dod_napake"]["splosna"] = "Napaka!. Poizkusite ponovno";
            }
            $baza->close();
        }
    }
    
    if(isset($_SESSION["dod_napake"])){
        $_SESSION["dod_input"]["vprasanje"] = $podatki["vprasanje"];
        $_SESSION["dod_input"]["odgovor_A"] = $podatki["odgovor_A"];
        $_SESSION["dod_input"]["odgovor_B"] = $podatki["odgovor_B"];
        $_SESSION["dod_input"]["odgovor_C"] = $podatki["odgovor_C"];
        $_SESSION["dod_input"]["odgovor_D"] = $podatki["odgovor_D"];
        header("location: dodaj_vprasanje.php?id_kviza=" . $_GET["id_kviza"]);
        exit();
    }
    else{
        header("location: uredi_kviz.php?id_kviza=" . $_GET["id_kviza"]);
        exit();
    }

?>