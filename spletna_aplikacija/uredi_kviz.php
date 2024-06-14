<?php
    session_start();
    if(!isset($_SESSION["id_uporabnika"])){
        header("Location: prijava");
        exit();
    }
    else if(isset($_GET["id_kviza"])){
        require("podatki.php");
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
    <title>Uredi kviz</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="kvizi.css?verzija=7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        
    </style> 
</head>
<body>
       <div id="naslov"><?php echo $naslov_kviza ?></div>
       <div id="vsebina">
            <?php
                require("podatki.php");
                $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
                if($baza->connect_error){
                    echo "<div id='napaka'> Povezava žal ni bila uspešna </div>";
                }
                else{
                    $pridobi_podatke = $baza->prepare("SELECT id_vprasanja, vprasanje, odgovor_A, odgovor_B, odgovor_C, odgovor_D, pravilen FROM vprasanja WHERE id_kviza = ? ORDER BY datum_vprasanja");
                    $pridobi_podatke->bind_param("i", $_GET["id_kviza"]);
                    if($pridobi_podatke->execute()){
                        $rezultat = $pridobi_podatke->get_result();
                        if($rezultat->num_rows == 0){
                            echo "<div id='brez'> Trenutno nimate nobenega vprašanja </div>";
                        }
                        else{
                            $i=1;
                            while($vrstica = $rezultat->fetch_assoc()){
                                $vprasanje = '<span>' . $i . '. </span>' . $vrstica["vprasanje"];
                                $odgovori["A"] = $vrstica["odgovor_A"];
                                $odgovori["B"] = $vrstica["odgovor_B"];
                                $odgovori["C"] = $vrstica["odgovor_C"];
                                $odgovori["D"] = $vrstica["odgovor_D"];
                                echo dodaj_vrstico($vrstica["id_vprasanja"], $vprasanje, $odgovori, $vrstica["pravilen"]);
                                $i++;
                            }
                        }
                    }
                    $baza->close();
                }
            ?>
       </div>
       <div id="noga">
            <a id="nov" href="dodaj_vprasanje.php?id_kviza=<?php echo $_GET["id_kviza"] ?>"><i class="fas fa-plus"></i> dodaj vprašanje</a>
            <a id="nazaj" href="moji_kvizi.php">nazaj</a>
       </div>
       
</body>
</html>

<?php

    function dodaj_vrstico($id_vprasanja, $vprasanje, $odgovori, $pravilen){

        $pn = pravilno_napacno($odgovori, $pravilen);

        $izbrisi = "";
        if(isset($_SESSION["vprasanja"]["ne_prikazuj"])){
            $izbrisi = "&izbrisi=true";
        }    

        $prikaz = ' <div class="kviz">
                        <div class="vprasanje">
                            ' . $vprasanje .' 
                        </div>
                        <div class="odgovori">
                            <div> ' . $pn["A"] . ' <span class="crka">A</span> ' . $odgovori["A"] . ' </div>
                            <div> ' . $pn["B"] . ' <span class="crka">B</span> ' . $odgovori["B"] . ' </div>
                            <div> ' . $pn["C"] . ' <span class="crka">C</span> ' . $odgovori["C"] . ' </div>
                            <div> ' . $pn["D"] . ' <span class="crka">D</span> ' . $odgovori["D"] . ' </div>
                        </div>
                        <div class="moznosti">
                            <a href="statistika.php?id_vprasanja=' . $id_vprasanja . '"><i class="fas fa-poll-h"></i> statistika</a>
                            <a href="izbrisi_vprasanje.php?id_vprasanja=' . $id_vprasanja . $izbrisi . '"><i class="fas fa-trash-alt"></i> izbriši</a> 
                        </div>
                    </div> ';

        return $prikaz;
    }
    
    function pravilno_napacno($odgovori, $pravilen){
        foreach($odgovori as $crka => $odgovor){
            if($crka == $pravilen){
                $pn[$crka] = '<span class="znak" style="color: #00c853;">&#x2713</span> ';
            }
            else{
                $pn[$crka] = '<span class="znak">&#x2715</span> ';
            }
        }
        return $pn;
    }

?>
