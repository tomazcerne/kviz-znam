<?php
    session_start();
    if(!isset($_SESSION["id_uporabnika"])){
        $_SESSION["nazaj"] = "ustvari_kviz.php";
        header("Location: prijava?nazaj=true");
        exit();
    }
    $link = "..";
    if(isset($_GET["nazaj"])){
        $link = "moji_kvizi.php";
    }
    function napaka($input){
        if(isset($_SESSION["ust_napake"][$input])){
            $temp = $_SESSION["ust_napake"][$input];
            unset($_SESSION["ust_napake"][$input]);
            return $temp;
        }
        return "";
    }
    function vrni($input){
        if(isset($_SESSION["ust_input"][$input])){
            $temp = $_SESSION["ust_input"][$input];
            unset($_SESSION["ust_input"][$input]);
            return $temp;
        }
        return "";
    }    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ustvari nov kviz</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" type="text/css" href="registracija/pr_reg.css?verzija=5">
    <script type="text/javascript" src="registracija/pr_reg.js"></script>
    
    <style>
        *{
            font-family: Arial, Helvetica, sans-serif;
        }
        #ustvari_kviz{
            padding: 0;
            margin: 60px auto;
            width: 95%;
            max-width: 600px;
            transform: translate(0);
        }
        input[type=text], input[type=password], textarea{
            border: solid gray 1px;
            border-radius: 5px;
        }
        #naslov{
            margin-bottom: 15px;
        }
        a{
            font-size: 17px;
            margin-top: 10px;
            float: right;
            display: block;
            text-align: center;
            text-decoration: none;
            width: 100%;
            max-width: 200px;
            padding: 10px;
            color: white;
            background: #124fa0;
            border: solid 2px transparent;
            border-radius: 10px;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        }
        a:hover{
            background: #165ebd;
        }
        #submit{
           margin-left: 0;
           transform: translate(0); 
        }
        @media only screen and (max-width: 500px) {
            #ustvari_kviz { 
                margin-top: 20px;
            }
            a, #submit{
                float: none;
                margin-left: 50%;
                margin-bottom: 10px;
                transform: translate(-50%);

            }
        }
    </style>
    <script>

        var max = [];
        max["naslov_kviza"] = 50;
        max["opis"] = 300;
        max["geslo_kviza"] = 30;

        function preveri(input){
            napaka(input,"");
            let vnos = input.value.trim();
            if(vnos.length == 0 && input.id == "naslov_kviza"){
                napaka(input,"Obvezno izpolnite to polje");
            }
            else if(vnos.length > max[input.id]){
                napaka(input,"Vnešeni podatki naj ne presegajo " + max[input.id] + " znakov");
            }   
        }

    </script>   
</head>
<body>
        <form id="ustvari_kviz" action="ustvari.php" method="post" autocomplete="off" onsubmit="return potrditev()">
            <div id="naslov">
                Ustvari nov kviz
            </div>
            <div class="vnos">
                <input type="text" id="naslov_kviza" name="naslov_kviza" placeholder="Naslov kviza" value="<?php echo vrni("naslov_kviza"); ?>" onkeyup="preveri(this)">
                <div class="napaka"> <?php echo napaka("naslov_kviza"); ?> </div>
            </div>
            <div class="vnos">
                <textarea rows="5" id="opis" name="opis" placeholder="Opis (izbirno)"><?php echo vrni("opis"); ?></textarea>
                <div class="napaka"> <?php echo napaka("opis"); ?> </div>
            </div>
            <div class="vnos">
                <input type="text" id="geslo_kviza" name="geslo_kviza" placeholder="Geslo kviza (izbirno)" onkeyup="preveri(this)">
                <div class="napaka"> <?php echo napaka("geslo_kviza"); ?> </div>
                <div class="pokazi">
                    <input type="checkbox" checked="true" id="pg1" onclick="pokaziGeslo(this)"> 
                    <label for="pg1"> pokaži geslo </label> 
                </div>
            </div>
            <div id="omejitev">
                <input type="checkbox" checked="true" id="enkrat" name="enkrat" value="true">
                <label for="enkrat"> Vsak igralec lahko kviz igra le enkrat</label>
            </div>
            <div id="splosna_napaka">
                <?php echo napaka("splosna");?>
            </div>
            <button id="submit" type ="submit"> Ustvari </button>
            <a id="preklici" href="<?php echo $link ?>">Prekliči</a>
        </form>
</body>
</html>