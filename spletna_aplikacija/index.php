<?php
    session_start();

    if(isset($_COOKIE["id_uporabnika"])){
        $_SESSION["id_uporabnika"] = $_COOKIE["id_uporabnika"];
        setcookie("id_uporabnika", $_SESSION["id_uporabnika"], time() + (86400 * 60), "/");
    }

    $povezava[0] = '<a href="prijava">prijava</a>';
    $povezava[1] = '<a href="registracija">registracija</a>';
    $napis = "Vpišite se";
    if(isset($_SESSION["id_uporabnika"])){
        require("podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT ime, priimek FROM uporabniki WHERE id_uporabnika = ?");
            $pridobi_podatke->bind_param("i", $_SESSION["id_uporabnika"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    $napis = $vrstica["ime"] . " " . $vrstica["priimek"];
                    $povezava[0] = '<a href="odjava.php">odjava</a>';
                    $povezava[1] = '<a href="izbrisi_racun.php">izbriši račun</a>';
                }
            }
            $baza->close();
        }
        
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kviz Znam</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Zanimiva spletna igra, v kateri lahko rešujete najrazličnejše kvize, ustvarjate svoje, spremljate rezultate...">
    <meta name="keywords" content="kviz, znam, kviz znam, igra" >
    <meta name="author" content="Tomaž Černe">
    <link rel="stylesheet" type="text/css" href="naslovnica.css?verzija=9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
</head>
<body>
    <div id="naslovna_vrstica">
        <div id="naslov">
            Kviz Znam
            <i class="fas fa-question"></i>
        </div>
        <div id="meni" >
            <button id="gumb_meni">
                <i class="fa fa-bars"></i>
            </button>
            <div id="spustni_seznam">
                <a href="kvizi.php">igraj kviz</a>
                <a href="moji_rezultati.php">moji rezultati</a>
                <a href="moji_kvizi.php">moji kvizi</a>
                <a href="ustvari_kviz.php">ustvari nov kviz</a>
                <a href="info.html">o spletni strani</a>
            </div>
        </div>
        <div id="up_meni" >
            <button id="gumb_up_meni">
                <i class="fa fa-user-circle"></i>
                <span id="napis"> <?php echo $napis?> </span>
                <i class="fa fa-caret-down" id="puscica"></i>
            </button>
            <div id="up_spustni_seznam">
                <?php 
                    for($i=0; $i<count($povezava); $i++){
                        echo $povezava[$i];
                    } 
                ?>
            </div>
        </div>
        
    </div>
    <div id="vsebina">
        <a href="kvizi.php">
            <i class="fas fa-play-circle"></i> <br>
            Igraj kviz
        </a>
        <a href="moji_rezultati.php">
            <i class="fas fa-poll"></i> <br>
            Moji rezultati
        </a>
        <a href="moji_kvizi.php">
            <i class="fas fa-list-ul"></i> <br>
            Moji kvizi
        </a>
        <a href="ustvari_kviz.php">
            <i class="fas fa-plus"></i> <br>
            Ustvari nov kviz
        </a>
        <a href="info.html">
            <i class="fas fa-info-circle"></i> <br>
            O spletni strani
        </a>
    </div>
    <div id="sporocilo">
        <i class="fas fa-exclamation"></i>
        dragi obiskovalec, preizkusite se v reševanju in ustvarjanju kvizov.
    </div>
    
</body>
</html>