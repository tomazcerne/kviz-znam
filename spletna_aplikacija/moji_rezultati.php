<?php
    session_start();
    if(!isset($_SESSION["id_uporabnika"])){
        $_SESSION["nazaj"] = "moji_rezultati.php";
        header("Location: prijava?nazaj=true");
        exit();
    }
    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Moji rezultati</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="kvizi.css?verzija=7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
       <div id="naslov">Moji rezultati</div>
       <div id="vsebina">
            <?php
                require("podatki.php");
                $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
                if($baza->connect_error){
                    echo "<div id='napaka'> Povezava žal ni bila uspešna </div>";
                }
                else{
                    $pridobi_podatke = $baza->prepare("SELECT naslov_kviza, st_pravilnih, st_vprasanj, datum_rezultata
                        FROM kvizi INNER JOIN rezultati ON kvizi.id_kviza = rezultati.id_kviza AND rezultati.id_uporabnika = ? ORDER BY datum_rezultata DESC");
                    $pridobi_podatke->bind_param("i", $_SESSION["id_uporabnika"]);
                    if($pridobi_podatke->execute()){
                        $rezultat = $pridobi_podatke->get_result();
                        if($rezultat->num_rows == 0){
                            echo "<div id='brez'> Trenutno še nimate nobenih rezultatov </div>";
                        }
                        else{
                            while($vrstica = $rezultat->fetch_assoc()){
                                echo dodaj_vrstico($vrstica["naslov_kviza"], $vrstica["st_pravilnih"], $vrstica["st_vprasanj"], $vrstica["datum_rezultata"]);
                            }
                        }
                    }
                    $baza->close();
                }
            ?>   
       </div>
       <div id="noga">
            <a id="nazaj" href="..">nazaj</a>
       </div>
       
</body>
</html>

<?php

    function dodaj_vrstico($naslov, $st_pravilnih, $st_vprasanj, $datum_ura){
        
        $datum_ura = new DateTime($datum_ura);
        $datum = $datum_ura->format("d.m.Y");
        $ura = $datum_ura->format("H:i");

        $procent = round((100.0*$st_pravilnih)/$st_vprasanj);

        $prikaz = ' <div class="kviz">
                        <div class="naslov_kviza">
                            ' . $naslov .' - ' . $procent . '% 
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