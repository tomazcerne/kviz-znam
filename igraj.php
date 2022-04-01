<?php
    session_start();
    if(isset($_GET["id_kviza"])){
        $id_kviza = $_GET["id_kviza"];
    }
    else{
        header("Location: ..");
        exit();
    }
    if(!isset($_SESSION["id_uporabnika"])){
        $_SESSION["nazaj"] = "igraj.php?id_kviza=" . $id_kviza;
        header("Location: prijava?nazaj=true");
        exit();
    }
    if(!isset($_POST["geslo_kviza"])){
        header("Location: vstop.php?id_kviza=". $id_kviza);
        exit();
    }
    else if(trim($_POST["geslo_kviza"]) == ""){
        $_SESSION["vst_napake"]["geslo"] = "Obvezno izpolnite to polje";
        header("Location: vstop.php?id_kviza=". $id_kviza);
        exit();
    }
    else{
        $geslo = md5(trim($_POST["geslo_kviza"]));
        if($_POST["geslo_kviza"] == md5("")){
            $geslo = md5("");
        }
        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT geslo_kviza, id_uporabnika FROM kvizi WHERE id_kviza = ?");
            $pridobi_podatke->bind_param("i", $id_kviza);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    if($vrstica["geslo_kviza"] != $geslo){
                        $_SESSION["vst_napake"]["geslo"] = "Geslo je napačno";
                        header("Location: vstop.php?id_kviza=". $id_kviza);
                        exit();
                    }
                    if($vrstica["id_uporabnika"] == $_SESSION["id_uporabnika"]){
                        header("Location: lasten_kviz.html");
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

    $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
    if($baza->connect_error){
        header("Location: odjava.php");
        exit();
    }
    else{
        $pridobi_podatke = $baza->prepare("SELECT id_vprasanja FROM vprasanja WHERE id_kviza = ?");
        $pridobi_podatke->bind_param("i", $id_kviza);
        if($pridobi_podatke->execute()){
            $rezultat = $pridobi_podatke->get_result();
            if($rezultat->num_rows > 0){
                $i = 0;
                while($vrstica = $rezultat->fetch_assoc()){
                    $vprasanja[$i] = $vrstica["id_vprasanja"];
                    $i++;
                } 
            }
            else{
                header("Location: prazen_kviz.html");
                exit();
            }
        }
        else{
            header("Location: ../");
            exit();
        }
        $baza->close();
    }

    $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
    if($baza->connect_error){
        header("Location: odjava.php");
        exit();
    }
    else{

        $preveri = $baza->prepare("SELECT id_rezultata FROM rezultati WHERE id_uporabnika = ? AND id_kviza = ?");
        $preveri->bind_param("ii", $_SESSION["id_uporabnika"], $id_kviza);
        $preveri->execute();
        $rezultat = $preveri->get_result();
        if($rezultat->num_rows > 0){
            $preveri = $baza->prepare("SELECT enkrat FROM kvizi WHERE id_kviza = ?");
            $preveri->bind_param("i", $id_kviza);
            $preveri->execute();
            $rezultat = $preveri->get_result();
            if($rezultat->num_rows > 0){
                $vrstica = $rezultat->fetch_assoc();
                if($vrstica["enkrat"]==1){
                    header("Location: zavrnitev.html");
                    exit();
                }
            }
        }


        $ustvari_rezultat = $baza->prepare("INSERT INTO rezultati(id_kviza, id_uporabnika, st_vprasanj, datum_rezultata) VALUES (?,?,?,?)");
        $v = sizeof($vprasanja);
        $d = date("Y-m-d H:i:s");
        $ustvari_rezultat->bind_param("iiis", $id_kviza, $_SESSION["id_uporabnika"], $v, $d);
        if($ustvari_rezultat->execute()){
            
            $pridobi_podatke = $baza->prepare("SELECT id_rezultata FROM rezultati WHERE datum_rezultata = ?");
            $pridobi_podatke->bind_param("s", $d);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    $id_rezultata = $vrstica["id_rezultata"];
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
        }
        else{
            header("Location: ../");
            exit();
        }
        $baza->close();
    }


    
    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Igraj</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fontawesome-free-5.15.4-web/css/all.css">
    <link rel="stylesheet" href="igra.css?verzija=12">
    <script type="text/javascript" src="igra.js?verzija=2"></script>
    <style>
       
    </style>
    <script>

        window.onload = function(){
            document.getElementById("polje").style.display = "block";
            naprej();
        };

        window.onbeforeunload = function(e){
            e.returnValue = "";
        };

        const vprasanja = [];
        <?php
            for($i=0; $i<sizeof($vprasanja); $i++){
                echo 'vprasanja[' . $i . '] = ' . $vprasanja[$i] . ';';
            }
        ?>
        var trenutno_vprasanje = -1;
        <?php
            echo "var id_rezultata = " . $id_rezultata . ";";
        ?>

    </script>
    <noscript>
        <div id="napaka" style="margin: 40px 10px;">Brskalniku je onemogočena uporaba Javascript jezika, ki je nujno potreben za delovanje tega kviza!</div>
    </noscript>

</head>
<body>
    <div id="polje">
        <div id="prikaz">
           
        </div> 
        <div id="sporocilo">
            Izberi dva odgovora, med katerima bo nato joker izločil napačnega
        </div>
        <div id="pomoci">
            <button class="pomoc" id="polovicka" onclick="polovicka()">polovička</button>
            <button class="pomoc" id="glas_ljudstva" onclick="glas_ljudstva()">glas ljudstva</button>
            <button class="pomoc" id="joker" onclick="joker()">joker</button>
        </div>
        <div id="nadaljuj">
            <button id="naprej" onclick="naprej()">naprej</button>
        </div>
        <div id="rezultati">
            <?php
                for($i=0; $i<sizeof($vprasanja); $i++){
                    echo '<div class="rezultat">' . ($i + 1) . '</div>';
                }
            ?> 
        </div>
    </div>

    <div id="polje_glas_ljudstva">
        <div id="naslov">
            glas ljudstva
        </div>
        <div class="odg">A</div>
        <div class="cela_vrstica">
            <div class="procent">0%</div>
        </div>

        <div class="odg">B</div>
        <div class="cela_vrstica">
            <div class="procent">0%</div>
        </div>

        <div class="odg">C</div>
        <div class="cela_vrstica">
            <div class="procent">0%</div>
        </div>

        <div class="odg">D</div>
        <div class="cela_vrstica">
            <div class="procent">0%</div>
        </div>

        <button id="glas_ljudstva_izhod" onclick="zapri_glas_ljudstva()">nazaj</button>
    </div>

    <div id="konec">
        <div id="napis">
            Čestitke, prišel si do konca kviza.
        </div>
        <div class="koncni_rezultati">
            Končni rezultat: <span id="pravilnih"></span>/<span id="skupno"></span>
        </div>
        <div class="koncni_rezultati">
            Uspešnost: <span id="uspesnost"></span>
        </div>

        <a href="moji_rezultati.php" id="zakljuci">zaključi</a>
    </div>
    
</body>
</html>