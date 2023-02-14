<?php
    session_start();

    if(isset($_SESSION["id_uporabnika"])){
        $naprej = "../";
        if(isset($_GET["nazaj"])){
            $naprej .= $_SESSION["nazaj"];
        }
        header("location: " . $naprej);
        exit();
    }

    function napaka($input){
        if(isset($_SESSION["reg_napake"][$input])){
            $temp = $_SESSION["reg_napake"][$input];
            unset($_SESSION["reg_napake"][$input]);
            return $temp;
        }
        return "";
    }
    function vrni($input){
        if(isset($_SESSION["reg_input"][$input])){
            $temp = $_SESSION["reg_input"][$input];
            unset($_SESSION["reg_input"][$input]);
            return $temp;
        }
        return "";
    }
    $action = "registracija.php";
    $pri_url = "../prijava";
    if(isset($_GET["nazaj"])){
        $action .= "?nazaj=true";
        $pri_url .= "?nazaj=true";
    }        
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registracija</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="pr_reg.css?verzija=5">
    <script type="text/javascript" src="reg.js"></script>
    <script type="text/javascript" src="pr_reg.js"></script>
</head>
<body>
    <div id="polje">

        <a id="zapri" href="..">&#x2715</a>

        <form id="registracija" action="<?php echo $action ?>" method="post" autocomplete="off" onsubmit="return potrditev()">
            <div id="naslov">
            Ustvari nov račun
            </div>
            <div class="vnos">
                <input type="text" id="ime" name="ime" placeholder="Ime" value="<?php echo vrni("ime"); ?>" onkeyup="preveri(this)" > 
                <div class="napaka"> <?php echo napaka("ime"); ?> </div>
            </div>
            <div class="vnos">
                <input type="text" id="priimek" name="priimek" placeholder="Priimek" value="<?php echo vrni("priimek"); ?>" onkeyup="preveri(this)">
                <div class="napaka"> <?php echo napaka("priimek"); ?> </div>
            </div>
            <div class="vnos">
                <input type="text" id="uporabnisko_ime" name="uporabnisko_ime" placeholder="Uporabniško ime" value="<?php echo vrni("uporabnisko_ime"); ?>" onkeyup="preveri(this)">
                <div class="napaka"> <?php echo napaka("uporabnisko_ime"); ?> </div>
            </div>
            <div class="vnos">
                <input type="password" id="geslo" name="geslo" placeholder="Geslo" onkeyup="preveri(this)">
                <div class="napaka"> <?php echo napaka("geslo"); ?> </div>
                <div class="pokazi">
                    <input type="checkbox" id="pg1" onclick="pokaziGeslo(this)"> 
                    <label for="pg1"> pokaži geslo </label> 
                </div>
            </div>
            <div class="vnos">
                <input type="password" id="potrdi_geslo" name="potrdi_geslo" placeholder="Potrdi geslo" onkeyup="preveri(this)">
                <div class="napaka"> <?php echo napaka("potrdi_geslo"); ?> </div>
                <div class="pokazi">
                    <input type="checkbox" id="pg2" onclick="pokaziGeslo(this)"> 
                    <label for="pg2"> pokaži geslo </label>
                </div>
            </div>
            <div id="preusmeritev">
                Že imaš svoj račun? <a href="<?php echo $pri_url ?>"> Prijavi se </a>
            </div>
            <div id="splosna_napaka">
                <?php echo napaka("splosna");?>
            </div>
            
            <button id="submit" type ="submit"> Registracija </button>
            
        </form>

    </div>

</body>
</html>
 



