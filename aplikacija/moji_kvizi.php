<?php
    session_start();
    if(!isset($_SESSION["id_uporabnika"])){
        $_SESSION["nazaj"] = "moji_kvizi.php";
        header("Location: prijava?nazaj=true");
        exit();
    }
    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Moji kvizi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="kvizi.css?verzija=7">
    <link rel="stylesheet" href="fontawesome-free-5.15.4-web/css/all.css">
    <style>
        
    </style> 
</head>
<body>
       <div id="naslov">Moji kvizi</div>
       <div id="vsebina">
            <?php
                require("../podatki/podatki.php");
                $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
                if($baza->connect_error){
                    echo "<div id='napaka'> Povezava žal ni bila uspešna </div>";
                }
                else{
                    $pridobi_podatke = $baza->prepare("SELECT id_kviza, naslov_kviza, datum_kviza FROM kvizi WHERE id_uporabnika = ? ORDER BY datum_kviza DESC");
                    $pridobi_podatke->bind_param("i", $_SESSION["id_uporabnika"]);
                    if($pridobi_podatke->execute()){
                        $rezultat = $pridobi_podatke->get_result();
                        if($rezultat->num_rows == 0){
                            echo "<div id='brez'> Trenutno nimate nobenega kviza </div>";
                        }
                        else{
                            while($vrstica = $rezultat->fetch_assoc()){

                                $prestej_vprasanja = $baza->prepare("SELECT COUNT(id_vprasanja) AS st_vprasanj FROM vprasanja WHERE id_kviza=?");
                                $prestej_vprasanja->bind_param("i", $vrstica["id_kviza"]);
                                if($prestej_vprasanja->execute()){
                                    $rezultat1 = $prestej_vprasanja->get_result();
                                    $vrstica1 = $rezultat1->fetch_assoc();
                                }

                                $prestej_rezultate = $baza->prepare("SELECT COUNT(id_rezultata) AS st_rezultatov FROM rezultati WHERE id_kviza=?");
                                $prestej_rezultate->bind_param("i", $vrstica["id_kviza"]);
                                if($prestej_rezultate->execute()){
                                    $rezultat2 = $prestej_rezultate->get_result();
                                    $vrstica2 = $rezultat2->fetch_assoc();
                                }
                                
                                echo dodaj_vrstico($vrstica["id_kviza"], $vrstica["naslov_kviza"], $vrstica["datum_kviza"], $vrstica1["st_vprasanj"], $vrstica2["st_rezultatov"]);
                            }
                        }
                    }
                    $baza->close();
                }
            ?>   
       </div>
       <div id="noga">
            <a id="nov" href="ustvari_kviz.php?nazaj=true"><i class="fas fa-plus"></i> ustvari nov kviz</a>
            <a id="nazaj" href="..">nazaj</a>
       </div>
       
</body>
</html>

<?php

    function dodaj_vrstico($id_kviza, $naslov, $datum_ura, $st_vprasanj, $st_rezultatov){
        
        $izbrisi = "";
        if(isset($_SESSION["kvizi"]["ne_prikazuj"])){
            $izbrisi = "&izbrisi=true";
        }    

        $datum_ura = new DateTime($datum_ura);
        $datum = $datum_ura->format("d.m.Y");
        $ura = $datum_ura->format("H:i");

        $prikaz = ' <div class="kviz">
                        <div class="naslov_kviza">
                            ' . $naslov .' 
                        </div>
                        <div class="podatki">
                            <i class="far fa-calendar"></i> ' . $datum . ' 
                            <i class="far fa-clock"></i> ' . $ura . '
                            <i class="fas fa-question"></i> ' . $st_vprasanj . '
                            <i class="far fa-play-circle"></i> ' . $st_rezultatov . ' 
                        </div>
                        <div class="moznosti">
                            <a href="uredi_kviz.php?id_kviza=' . $id_kviza . '"><i class="fas fa-edit"></i> uredi</a>
                            <a href="rezultati.php?id_kviza=' . $id_kviza . '"><i class="fas fa-poll"></i> rezultati</a>
                            <a href="izbrisi_kviz.php?id_kviza=' . $id_kviza . $izbrisi . '"><i class="fas fa-trash-alt"></i> izbriši</a>
                        </div>
                    </div> ';

        return $prikaz;
    } 

?>