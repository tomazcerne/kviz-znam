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
            $pridobi_podatke = $baza->prepare("SELECT id_uporabnika, naslov_kviza FROM kvizi WHERE id_kviza = ?");
            $pridobi_podatke->bind_param("i", $_GET["id_kviza"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    if($vrstica["id_uporabnika"] != $_SESSION["id_uporabnika"]){
                        header("Location: ../");
                        exit();
                    }
                    $naslov_kviza = $vrstica["naslov_kviza"];
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
    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rezultati</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="kvizi.css?verzija=7">
    <link rel="stylesheet" href="fontawesome-free-5.15.4-web/css/all.css">
    <style>
        #nazaj{
            float: none;
            margin-left: 50%;
            transform: translate(-50%);
        }
        .kviz{
            padding-top: 15px;
            padding-bottom: 15px;
        }
        @media only screen and (max-width: 600px) {
            #nazaj{
                margin-top: 0;
            }
            .kviz{
                padding-top: 10px;
                padding-bottom: 10px;
            }
        }
    </style> 
</head>
<body>
       <div id="naslov"><?php echo $naslov_kviza ?> - rezultati</div>
       <div id="vsebina">
            <?php
                require("../podatki/podatki.php");
                $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
                if($baza->connect_error){
                    echo "<div id='napaka'> Povezava žal ni bila uspešna </div>";
                }
                else{
                    $pridobi_podatke = $baza->prepare("SELECT uporabnisko_ime, st_pravilnih, st_vprasanj, datum_rezultata
                        FROM uporabniki INNER JOIN rezultati ON uporabniki.id_uporabnika = rezultati.id_uporabnika AND rezultati.id_kviza = ? ORDER BY 1.0*st_pravilnih/st_vprasanj DESC, datum_rezultata ASC");
                    $pridobi_podatke->bind_param("i", $_GET["id_kviza"]);
                    if($pridobi_podatke->execute()){
                        $rezultat = $pridobi_podatke->get_result();
                        if($rezultat->num_rows == 0){
                            echo "<div id='brez'> Trenutno še ni nobenih rezultatov </div>";
                        }
                        else{
                            $i = 1;
                            while($vrstica = $rezultat->fetch_assoc()){
                                echo dodaj_vrstico($vrstica["uporabnisko_ime"], $vrstica["st_pravilnih"], $vrstica["st_vprasanj"], $vrstica["datum_rezultata"], $i);
                                $i++;
                            }
                        }
                    }
                    $baza->close();
                }
            ?>   
       </div>
       <div id="noga">
            <a id="nazaj" href="moji_kvizi.php">nazaj</a>
       </div>
       
</body>
</html>

<?php

    function dodaj_vrstico($uporabnisko_ime, $st_pravilnih, $st_vprasanj, $datum_ura, $i){
        
        $datum_ura = new DateTime($datum_ura);
        $datum = $datum_ura->format("d.m.Y");
        $ura = $datum_ura->format("H:i");

        $procent = round((100.0*$st_pravilnih)/$st_vprasanj);

        $prikaz = ' <div class="kviz">
                        <div class="naslov_kviza">
                            ' . $i . '. ' . $uporabnisko_ime .' - ' . $procent . '% 
                        </div>
                        <div class="podatki">
                            <i class="far fa-check-circle"></i> <span style="color: #4abe4a; font-weight: bold;">' . $st_pravilnih . '</span>/' . $st_vprasanj . '
                            <i class="far fa-calendar"></i> ' . $datum . ' 
                            <i class="far fa-clock"></i> ' . $ura . '     
                        </div>
                    </div> ';

        return $prikaz;
    } 

?>