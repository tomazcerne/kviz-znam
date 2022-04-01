<?php
    session_start();

    if(isset($_COOKIE["id_uporabnika"])){
        $_SESSION["id_uporabnika"] = $_COOKIE["id_uporabnika"];
        setcookie("id_uporabnika", $_SESSION["id_uporabnika"], time() + (86400 * 60), "/");
    }

    if(isset($_SESSION["id_uporabnika"])){
        $naprej = "../";
        if(isset($_GET["nazaj"])){
            $naprej .= $_SESSION["nazaj"];
        }
        header("location: " . $naprej);
        exit();
    }

    function napaka($input){
        if(isset($_SESSION["pr_napake"][$input])){
            $temp = $_SESSION["pr_napake"][$input];
            unset($_SESSION["pr_napake"][$input]);
            return $temp;
        }
        return "";
    }
    function vrni($input){
        if(isset($_SESSION["pr_input"][$input])){
            $temp = $_SESSION["pr_input"][$input];
            unset($_SESSION["pr_input"][$input]);
            return $temp;
        }
        return "";
    }
    $action = "prijava.php";
    $reg_url = "../registracija";
    if(isset($_GET["nazaj"])){
        $action .= "?nazaj=true";
        $reg_url .= "?nazaj=true";
    }    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prijava</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../registracija/pr_reg.css?verzija=5">
    <script type="text/javascript" src="pr.js"></script>
    <script type="text/javascript" src="../registracija/pr_reg.js"></script>
</head>
<body>
    <div id="polje">

        <a id="zapri" href="..">&#x2715</a>

        <form id="prijava" action="<?php echo $action ?>" method="post" onsubmit="return potrditev()">
            <div id="naslov">
            Prijavi se
            </div>
            <div class="vnos">
                <input type="text" id="uporabnisko_ime" name="uporabnisko_ime" placeholder="Uporabniško ime" value="<?php echo vrni("uporabnisko_ime"); ?>" onkeyup="preveri(this)">
                <div class="napaka"> <?php echo napaka("uporabnisko_ime"); ?> </div>
            </div>
            <div class="vnos">
                <input type="password" id="geslo" name="geslo" placeholder="Geslo" value="<?php echo vrni("geslo"); ?>" onkeyup="preveri(this)">
                <div class="napaka"> <?php echo napaka("geslo"); ?> </div>
                <div class="pokazi">
                    <input type="checkbox" id="pg1" onclick="pokaziGeslo(this)"> 
                    <label for="pg1"> pokaži geslo </label> 
                </div>
            </div>
            <div id="ostani">
                <input type="checkbox" id="ostani_prijavljen" name="ostani_prijavljen" value="true">
                <label for="ostani_prijavljen"> ostani prijavljen</label>
            </div>
            <div id="preusmeritev">
                Še nimaš svojega računa? <a href="<?php echo $reg_url ?>"> Registriraj se </a>
            </div>
            <div id="splosna_napaka">
                <?php echo napaka("splosna");?>
            </div>
            <button id="submit" type ="submit"> Prijava </button>
            
        </form>

    </div>

</body>
</html>
 