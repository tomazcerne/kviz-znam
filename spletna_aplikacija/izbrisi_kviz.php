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
    
    if(isset($_GET["ne_prikazuj"])){
        $_SESSION["kvizi"]["ne_prikazuj"] = true;
    }
    if(isset($_GET["izbrisi"])){
        
        require("podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        $izbrisi = $baza->prepare("DELETE FROM kvizi WHERE id_kviza = ?");
        $izbrisi->bind_param("i", $_GET["id_kviza"]);
        $izbrisi->execute();
        $baza->close();
        header("Location: moji_kvizi.php");
        exit();
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Izbriši kviz</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="izbrisi.css?verzija=1"> 
</head>
<body>
    <form id="polje" action="izbrisi_kviz.php">
        <div id="naslov">
            Ste prepričani, da želite izbrisati kviz z naslovom <?php echo $naslov_kviza ?> in s tem tudi vsa pripadajoča vprašanja ter rezultate? 
        </div>
        <input type="hidden" name="id_kviza" value="<?php echo $_GET["id_kviza"] ?>">
        <input type="hidden" name="izbrisi" value="true">
        <div id="skrij">
            <input type="checkbox" id="ne_prikazuj" name="ne_prikazuj" value="true">
            <label for="ne_prikazuj"> Ne prikazuj več tega sporočila</label>
        </div >
        <button id="submit" type ="submit"> Izbriši </button>
        <a id="preklici" href="moji_kvizi.php">Prekliči</a>
    </form>      
</body>
</html>
