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
    
    if(isset($_GET["ne_prikazuj"])){
        $_SESSION["vprasanja"]["ne_prikazuj"] = true;
    }
    if(isset($_GET["izbrisi"])){
        
        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        $izbrisi = $baza->prepare("DELETE FROM vprasanja WHERE id_vprasanja = ?");
        $izbrisi->bind_param("i", $_GET["id_vprasanja"]);
        $izbrisi->execute();
        $baza->close();
        header("Location: uredi_kviz.php?id_kviza=" . $id_kviza);
        exit();
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Izbriši vprašanje</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="izbrisi.css?verzija=1"> 
</head>
<body>
    <form id="polje" action="izbrisi_vprasanje.php">
        <div id="naslov">
            Ste prepričani, da želite izbrisati to vprašanje in s tem tudi vse pripadajoče rezultate? 
        </div>
        <input type="hidden" name="id_vprasanja" value="<?php echo $_GET["id_vprasanja"] ?>">
        <input type="hidden" name="izbrisi" value="true">
        <div id="skrij">
            <input type="checkbox" id="ne_prikazuj" name="ne_prikazuj" value="true">
            <label for="ne_prikazuj"> Ne prikazuj več tega sporočila</label>
        </div >
        <button id="submit" type ="submit"> Izbriši </button>
        <a id="preklici" href="uredi_kviz.php?id_kviza=<?php echo $id_kviza ?>">Prekliči</a>
    </form>      
</body>
</html>
