<?php
    session_start();

    function napaka($input){
        if(isset($_SESSION["vst_napake"][$input])){
            $temp = $_SESSION["vst_napake"][$input];
            unset($_SESSION["vst_napake"][$input]);
            return $temp;
        }
        return "";
    }

    if(!isset($_GET["id_kviza"])){
        header("Location: ..");
        exit();
    }
    if(!isset($_SESSION["id_uporabnika"])){
        $_SESSION["nazaj"] = "vstop.php?id_kviza=" . $_GET["id_kviza"];
        header("Location: prijava?nazaj=true");
        exit();
    }
    require("../podatki/podatki.php");
    $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
     if($baza->connect_error){
        header("Location: odjava.php");
        exit();
    }
    else{
        $pridobi_podatke = $baza->prepare("SELECT naslov_kviza, opis, geslo_kviza FROM kvizi WHERE id_kviza = ?");
        $pridobi_podatke->bind_param("i", $_GET["id_kviza"]);
        if($pridobi_podatke->execute()){
            $rezultat = $pridobi_podatke->get_result();
            if($rezultat->num_rows > 0){
                $vrstica = $rezultat->fetch_assoc();
                $naslov_kviza = $vrstica["naslov_kviza"];
                $opis = $vrstica["opis"];
                $geslo = $vrstica["geslo_kviza"];
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
    
    $action = "igraj.php?id_kviza=" . $_GET["id_kviza"];
    
    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vstop</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="registracija/pr_reg.css?verzija=7">
    <script type="text/javascript" src="registracija/pr_reg.js"></script>
    <script type="text/javascript" src="prijava/pr.js"></script>
    <style>
        #polje{
            max-width: 500px;
        }
        #vstop{
            width: 80%;
        }
        #naslov{
            font-size: 27px;
        }
        #opis, #sporocilo{
            font-size: 18px;
            margin-bottom: 10px;
        }
        @media only screen and (max-width: 600px) {
            #opis, #sporocilo{
            font-size: 16px;
        }
        }
    </style>
    <script>

    </script> 
</head>
<body>
    <div id="polje">

        <a id="zapri" href="kvizi.php">&#x2715</a>

        <form id="vstop" action="<?php echo $action?>" method="post">
            <div id="naslov">
                <?php echo $naslov_kviza ?>
            </div>
            <div id="opis">
                <?php echo $opis ?>
            </div>
            <div id="sporocilo">
                Z vstopom v kviz se začnejo beležiti vaši rezultati.
                Če boste kviz zapustili predčasno, bodo vsi dotedanji rezultati dokončno shranjeni.
                Ne uporabljajte tipke "nazaj" in ne osvežujte spletnega mesta, sicer boste preusmerjeni izven kviza. 
                Prosimo, igrajte pošteno! Želimo vam veliko uspeha pri reševanju kviza.
            </div>
            <?php
                if($geslo == md5("")){
                    echo "<input type='hidden' name='geslo_kviza' value='". $geslo ."'>";
                }
                else{
                    echo '
                        <div class="vnos">
                            <input type="password" id="geslo_kviza" name="geslo_kviza" placeholder="Geslo kviza" onkeyup="preveri(this)">
                            <div class="napaka">' . napaka("geslo") . '</div>
                            <div class="pokazi">
                                <input type="checkbox" id="pg1" onclick="pokaziGeslo(this)"> 
                                <label for="pg1"> pokaži geslo </label> 
                            </div>
                        </div>
                    ';
                }
            ?>

            <button type="submit" id="submit">nadaljuj</button>
            
        </form>
    </div>
    
</body>
</html>