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

    unset($_SESSION["pr_napake"]);

    $podatki["uporabnisko_ime"] = trim($_POST["uporabnisko_ime"]);
    $podatki["geslo"] = trim($_POST["geslo"]);
    
    foreach($podatki as $input => $vnos){
        preveri($input, $vnos);
    }

    function preveri($input, $vnos){
        if(empty($vnos)){
            $_SESSION["pr_napake"][$input] = "Obvezno izpolnite to polje";
        }
    }

    if(!isset($_SESSION["pr_napake"])){
        require("../../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            $_SESSION["pr_napake"]["splosna"] = "Povezava žal ni bila uspešna. ";
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT id_uporabnika, geslo FROM uporabniki WHERE uporabnisko_ime = ?");
            $pridobi_podatke->bind_param("s", $podatki["uporabnisko_ime"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    if($vrstica["geslo"] == md5($podatki["geslo"])){
                        $_SESSION["id_uporabnika"] = $vrstica["id_uporabnika"];
                        if(isset($_POST["ostani_prijavljen"])){
                            setcookie("id_uporabnika", $vrstica["id_uporabnika"], time() + (86400 * 60), "/");
                        }
                    }
                    else{
                        $_SESSION["pr_napake"]["geslo"] = "Gelo je napačno";
                    }
                }
                else{
                    $_SESSION["pr_napake"]["uporabnisko_ime"] = "Račun s tem uporabniškim imenom ne obstaja";
                }
                    
            
            }
            else{
                $_SESSION["pr_napake"]["splosna"] = "Napaka! Poizkusite ponovno";
            }
            $baza->close();
        }
        
    }
    
    if(isset($_SESSION["pr_napake"])){
        $_SESSION["pr_input"]["uporabnisko_ime"] = $podatki["uporabnisko_ime"];
        $nazaj = "../prijava";
        if(isset($_GET["nazaj"])){
            $nazaj .= "?nazaj=true";
        }
        header("location: " . $nazaj);
        exit();
    }
    else{
        $naprej = "../";
        if(isset($_GET["nazaj"])){
            $naprej .= $_SESSION["nazaj"];
        }
        header("location: " . $naprej);
        exit();
    }

    
    ?>
