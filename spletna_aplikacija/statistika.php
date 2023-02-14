<?php
    session_start();
    if(!isset($_SESSION["id_uporabnika"])){
        header("Location: prijava");
        exit();
    }
    else if(isset($_GET["id_vprasanja"])){
        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT id_uporabnika, kvizi.id_kviza FROM kvizi INNER JOIN vprasanja ON kvizi.id_kviza=vprasanja.id_kviza WHERE id_vprasanja = ?");
            $pridobi_podatke->bind_param("i", $_GET["id_vprasanja"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    if($vrstica["id_uporabnika"] != $_SESSION["id_uporabnika"]){
                        header("Location: ../");
                        exit();
                    }
                    $id_kviza = $vrstica["id_kviza"];
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

    $st = array("A"=>0,"B"=>0,"C"=>0,"D"=>0);

        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: ../odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT COUNT(id_odgovora) AS st, odgovor FROM odgovori WHERE id_vprasanja = ? GROUP BY odgovor");
            $pridobi_podatke->bind_param("i", $_GET["id_vprasanja"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    while($vrstica = $rezultat->fetch_assoc()){
                        $st[$vrstica["odgovor"]] = $vrstica["st"];
                    }
                }
            }
            $baza->close();
        }

    $n = $st["A"] + $st["B"] + $st["C"] + $st["D"];

    if($n==0){
        $odstotek["A"]  = 0;
        $odstotek["B"]  = 0;
        $odstotek["C"]  = 0;
        $odstotek["D"]  = 0;
    }
    else{
        $odstotek["A"]  = round((100.0*$st["A"])/$n);
        $odstotek["B"]  = round((100.0*$st["B"])/$n);
        $odstotek["C"]  = round((100.0*$st["C"])/$n);
        $odstotek["D"]  = round((100.0*$st["D"])/$n);
    }
   

?>
<!DOCTYPE html>
<html>
<head>
    <title>Statistika</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="igra.css?verzija=12">
    
    <style>
        #polje{
            display: block;
        }
    </style>

</head>
<body>
    <div id="polje">
        <div id="naslov">
            statistika
        </div>
        <div class="odg">A</div>
        <div class="cela_vrstica">
            <div class="procent" style="width: <?php echo $odstotek["A"] . "%" ?>"><?php echo $odstotek["A"] . "%" ?></div>
        </div>

        <div class="odg">B</div>
        <div class="cela_vrstica">
            <div class="procent" style="width: <?php echo $odstotek["B"] . "%" ?>"><?php echo $odstotek["B"] . "%" ?></div>
        </div>

        <div class="odg">C</div>
        <div class="cela_vrstica">
            <div class="procent" style="width: <?php echo $odstotek["C"] . "%" ?>"><?php echo $odstotek["C"] . "%" ?></div>
        </div>

        <div class="odg">D</div>
        <div class="cela_vrstica">
            <div class="procent" style="width: <?php echo $odstotek["D"] . "%" ?>"><?php echo $odstotek["D"] . "%" ?></div>
        </div>

        <a id="glas_ljudstva_izhod" href="uredi_kviz.php?id_kviza=<?php echo $id_kviza ?>">nazaj</a>
    </div>    
</body>
</html>
