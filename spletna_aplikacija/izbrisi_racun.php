<?php
    session_start();
    if(!isset($_SESSION["id_uporabnika"])){
        header("Location: ../");
        exit();
    }
   
    if(isset($_GET["izbrisi"])){
        
        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        $izbrisi = $baza->prepare("DELETE FROM uporabniki WHERE id_uporabnika = ?");
        $izbrisi->bind_param("i", $_SESSION["id_uporabnika"]);
        $izbrisi->execute();
        $baza->close();
        header("Location: odjava.php");
        exit();
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Izbriši račun</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="izbrisi.css?verzija=1"> 
</head>
<body>
    <form id="polje" action="izbrisi_racun.php">
        <div id="naslov">
            Ste prepričani, da želite izbrisati svoj uporabniški račun in s tem tudi vse svoje kvize in rezultate? 
        </div>
        <input type="hidden" name="izbrisi" value="true">

        <button id="submit" type ="submit"> Izbriši </button>
        <a id="preklici" href="../">Prekliči</a>
    </form>      
</body>
</html>
