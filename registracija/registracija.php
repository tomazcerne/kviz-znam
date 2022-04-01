<?php
    session_start();

    if(isset($_SESSION["id_uporabnika"])){
        $naprej = "../";
        if(isset($_GET["nazaj"])){
            $naprej .= $_SESSION["nazaj"];
        }
        header("location: " . $naprej);
        exit();
    }

    unset($_SESSION["reg_napake"]);

    $podatki["ime"] = trim($_POST["ime"]);
    $podatki["priimek"] = trim($_POST["priimek"]);
    $podatki["uporabnisko_ime"] = trim($_POST["uporabnisko_ime"]);
    $podatki["geslo"] = trim($_POST["geslo"]);
    $podatki["potrdi_geslo"] = trim($_POST["potrdi_geslo"]);

    foreach($podatki as $input => $vnos){
        preveri($input, $vnos);
    }

    function preveri($input, $vnos){
        if(empty($vnos)){
            $_SESSION["reg_napake"][$input] = "Obvezno izpolnite to polje";
        }
        else if($input == "potrdi_geslo" && $vnos != trim($_POST["geslo"])){
            $_SESSION["reg_napake"][$input] = "Gesli se morata ujemati";
        }
        else if(mb_strlen($vnos, "UTF-8") > 30){
            $_SESSION["reg_napake"][$input] = "Vnešeni podatki naj ne presegajo 30 znakov";
        }
        else if($input == "uporabnisko_ime" || $input == "geslo"){
            $pl["uporabnisko_ime"] = "Uporabniško ime";
            $pl["geslo"] = "Geslo";
            if(strpos($vnos," ") != false){
                $_SESSION["reg_napake"][$input] = $pl[$input] . " naj ne vsebuje presledkov";
            }
            else if($input == "geslo" && !geslo($input, $vnos)){
                $_SESSION["reg_napake"][$input] = "Geslo mora vsebovati male črke, velike črke, številke in znake";
            }
            else if(mb_strlen($vnos, "UTF-8") < 6){
                $_SESSION["reg_napake"][$input] = $pl[$input] . " naj bo dolgo vsaj 6 znakov";
            }
        }
    }

    function geslo($input, $vnos){
        $male = false; $velike = false; $stevilke = false; $znaki = false;
        $tab = preg_split('//u', $vnos, -1, PREG_SPLIT_NO_EMPTY);
        for($i=0; $i<sizeof($tab); $i++){
            $x = $tab[$i];
            echo $x . "<br>";
            if(mb_strtoupper($x,"UTF-8") != mb_strtolower($x,"UTF-8")){
                if($x == mb_strtoupper($x,"UTF-8")){
                    $velike = true;
                }
                else{
                    $male = true;
                }
            }
            else if(is_numeric($x)){
                $stevilke = true;
            }
            else{
                $znaki = true;
            }
        }
        return $male && $velike && $stevilke && $znaki;
    }

    if(!isset($_SESSION["reg_napake"])){
        require("../../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            $_SESSION["reg_napake"]["splosna"] = "Povezava žal ni bila uspešna. ";
        }
        else{
            $preveri_u_ime = $baza->prepare("SELECT * FROM uporabniki WHERE uporabnisko_ime = ?");
            $preveri_u_ime->bind_param("s", $podatki["uporabnisko_ime"]);
            if($preveri_u_ime->execute()){
                if($preveri_u_ime->get_result()->num_rows == 0){
                    $dodaj_uporabnika = $baza->prepare("INSERT INTO uporabniki (ime, priimek, uporabnisko_ime, geslo, datum_registracije) VALUES (?,?,?,?,?)");
                    $g = md5($podatki["geslo"]);
                    $d = date("Y-m-d H:i:s");
                    $dodaj_uporabnika->bind_param("sssss", $podatki["ime"], $podatki["priimek"], $podatki["uporabnisko_ime"], $g, $d);
                    if(!$dodaj_uporabnika->execute()){
                        $_SESSION["reg_napake"]["splosna"] = "Napaka!. Poizkusite ponovno";
                    }
                }
                else{
                    $_SESSION["reg_napake"]["uporabnisko_ime"] = "To uporabniško ime že uporablja nekdo drug";
                }
            }
            else{
                $_SESSION["reg_napake"]["splosna"] = "Napaka! Poizkusite ponovno";
            }
            $baza->close();
        }
        
    }
    
    $nazaj = "";
    if(isset($_GET["nazaj"])){
        $nazaj .= "?nazaj=true";
    }
    if(isset($_SESSION["reg_napake"])){
        $_SESSION["reg_input"]["ime"] = $podatki["ime"];
        $_SESSION["reg_input"]["priimek"] = $podatki["priimek"];
        $_SESSION["reg_input"]["uporabnisko_ime"] = $podatki["uporabnisko_ime"];
        header("location: ../registracija" . $nazaj);
        exit();
    }
    else{
        $_SESSION["pr_input"]["uporabnisko_ime"] = $podatki["uporabnisko_ime"];
        $_SESSION["pr_input"]["geslo"] = $podatki["geslo"];
        header("location: ../prijava" . $nazaj);
        exit();
    }

?>