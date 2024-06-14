<?php

    require("podatki.php");
    $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
    if($baza->connect_error){
        echo "<div id='napaka'> Povezava žal ni bila uspešna </div>";
    }
    else{
        $isci = "%%";
        if(isset($_POST["isci"])){
            $isci = "%" . trim($_POST["isci"]) . "%";
        }
        $pridobi_podatke = $baza->prepare("SELECT id_kviza, naslov_kviza, datum_kviza, uporabnisko_ime FROM kvizi INNER JOIN uporabniki 
            ON kvizi.id_uporabnika = uporabniki.id_uporabnika WHERE naslov_kviza LIKE ? ORDER BY datum_kviza DESC LIMIT 100");
        $pridobi_podatke->bind_param("s", $isci);
        if($pridobi_podatke->execute()){
            $rezultat = $pridobi_podatke->get_result();
            if($rezultat->num_rows == 0){
                echo "<div id='brez'> Žal nismo našli nobenega kviza </div>";
            }
            else{
                while($vrstica = $rezultat->fetch_assoc()){

                    $prestej_vprasanja = $baza->prepare("SELECT COUNT(id_vprasanja) AS st_vprasanj FROM vprasanja WHERE id_kviza=?");
                    $prestej_vprasanja->bind_param("i", $vrstica["id_kviza"]);
                    if($prestej_vprasanja->execute()){
                        $rezultat1 = $prestej_vprasanja->get_result();
                        $vrstica1 = $rezultat1->fetch_assoc();
                    }

                    echo dodaj_vrstico($vrstica["id_kviza"], $vrstica["naslov_kviza"], $vrstica["datum_kviza"], $vrstica["uporabnisko_ime"], $vrstica1["st_vprasanj"]);
                }
            }
        }
        $baza->close();
    }

    function dodaj_vrstico($id_kviza, $naslov, $datum_ura, $uporabnisko_ime, $st_vprasanj){

        $datum_ura = new DateTime($datum_ura);
        $datum = $datum_ura->format("d.m.Y");
        $ura = $datum_ura->format("H:i");

        $prikaz = ' <div class="kviz">
                        <div class="naslov_kviza">
                            ' . $naslov .' 
                        </div>
                        <div class="podatki">
                            <i class="fas fa-user"></i> ' . $uporabnisko_ime . ' 
                            <i class="far fa-calendar"></i> ' . $datum . ' 
                            <i class="far fa-clock"></i> ' . $ura . '
                            <i class="fas fa-question"></i> ' . $st_vprasanj . '  
                        </div>
                        <div class="moznosti">
                            <a href="igraj.php?id_kviza=' . $id_kviza . '"><i class="fas fa-play"></i> igraj</a>
                            <a href="top10.php?id_kviza=' . $id_kviza . '"><i class="fas fa-trophy"></i> top 10</a>
                        </div>
                    </div> ';

        return $prikaz;
    } 
?>