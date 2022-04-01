<?php
    session_start();
    if(!isset($_SESSION["id_uporabnika"])){
        header("Location: prijava");
        exit();
    }
    else if(isset($_GET["id_kviza"])){
        require("../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT id_uporabnika FROM kvizi WHERE id_kviza = ?");
            $pridobi_podatke->bind_param("i", $_GET["id_kviza"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();
                    if($vrstica["id_uporabnika"] != $_SESSION["id_uporabnika"]){
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
        
    }
    else{
        header("Location: ../");
        exit();
    }
    function napaka($input){
        if(isset($_SESSION["dod_napake"][$input])){
            $temp = $_SESSION["dod_napake"][$input];
            unset($_SESSION["dod_napake"][$input]);
            return $temp;
        }
        return "";
    }
    function vrni($input){
        if(isset($_SESSION["dod_input"][$input])){
            $temp = $_SESSION["dod_input"][$input];
            unset($_SESSION["dod_input"][$input]);
            return $temp;
        }
        return "";
    }    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dodaj vprašanje</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" type="text/css" href="registracija/pr_reg.css?verzija=5">
    <style>
        *{
            font-family: Arial, Helvetica, sans-serif;
        }
        input[type=radio]{
            float: left;
            height: 30px;
            
        }
        .vnos{
            text-align: center;
        }
        .napaka{
            width: 74%;
            margin-left: 50%;
            transform: translate(-50%);
            text-align: left;
        }
        #n1{
            width: 100%;
            margin-left: 5px;
            transform: translate(0);
        }
        #odgovor_A, #odgovor_B, #odgovor_C, #odgovor_D{
            width: 80%;
        }

        #dodaj_vprasanje{
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
        p{
            color: gray;
            margin-left: 5px;
        }
        p span{
            color: #00c853; 
            font-weight: bold
        }
        @media only screen and (max-width: 500px) {
            #dodaj_vprasanje { 
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
        max["vprasanje"] = 200;
        max["odgovor_A"] = 50;
        max["odgovor_B"] = 50;
        max["odgovor_C"] = 50;
        max["odgovor_D"] = 50;

        function preveri(input){
            napaka(input,"");
            let vnos = input.value.trim();
            if(vnos.length == 0){
                napaka(input,"Obvezno izpolnite to polje");
            }
            else if(vnos.length > max[input.id]){
                napaka(input,"Vnešeni podatki naj ne presegajo " + max[input.id] + " znakov");
            }   
        }
        function napaka(input, txt){
            let x = input.parentElement.lastElementChild;
            x.innerHTML = txt;
        }
        function potrditev(){
            let dovoli = true;
            let tab = document.getElementsByClassName("vnos");
            for(let i=0; i<tab.length; i++){
                let input = tab[i].firstElementChild;
                preveri(input);
                if(input.parentElement.lastElementChild.innerHTML != ""){
                    dovoli = false;
                }
            }
            tab = document.getElementsByClassName("pravilen");
            if(!(tab[0].checked || tab[1].checked || tab[2].checked || tab[3].checked)){
                dovoli = false;
                document.getElementById("splosna_napaka").innerHTML = "Označite, kateri izmed odgovorov je pravilen";
            }
            else{
                document.getElementById("splosna_napaka").innerHTML = "";
            } 
            return dovoli;  
        }

    </script>   
</head>
<body>
        <form id="dodaj_vprasanje" action="<?php echo "dodaj.php?id_kviza=" . $_GET["id_kviza"]; ?>" method="post" autocomplete="off" onsubmit="return potrditev()">
            <div id="naslov">
                Dodaj vprašanje
            </div>
            <div class="vnos">
                <input type="text" id="vprasanje" name="vprasanje" placeholder="Vprašanje" value="<?php echo vrni("vprasanje"); ?>" onkeyup="preveri(this)">
                <div class="napaka" id="n1"> <?php echo napaka("vprasanje"); ?> </div>
            </div>
            <p><span> &#x2713</span> (označi)</p>
            <div class="vnos">
                <input type="text" id="odgovor_A" name="odgovor_A" placeholder="Odgovor A" value="<?php echo vrni("odgovor_A"); ?>" onkeyup="preveri(this)">
                <input type="radio" class="pravilen" id="A" name="pravilen" value="A">
                <div class="napaka"> <?php echo napaka("odgovor_A"); ?> </div>
            </div>
            <div class="vnos">
                <input type="text" id="odgovor_B" name="odgovor_B" placeholder="Odgovor B" value="<?php echo vrni("odgovor_B"); ?>" onkeyup="preveri(this)">
                <input type="radio" class="pravilen" id="B" name="pravilen" value="B">
                <div class="napaka"> <?php echo napaka("odgovor_B"); ?> </div>
            </div>
            <div class="vnos">
                <input type="text" id="odgovor_C" name="odgovor_C" placeholder="Odgovor C" value="<?php echo vrni("odgovor_C"); ?>" onkeyup="preveri(this)">
                <input type="radio" class="pravilen" id="C" name="pravilen" value="C">
                <div class="napaka"> <?php echo napaka("odgovor_C"); ?> </div>
            </div>
            <div class="vnos">
                <input type="text" id="odgovor_D" name="odgovor_D" placeholder="Odgovor D" value="<?php echo vrni("odgovor_D"); ?>" onkeyup="preveri(this)">
                <input type="radio" class="pravilen" id="D" name="pravilen" value="D">
                <div class="napaka"> <?php echo napaka("odgovor_D"); ?> </div>
            </div>
            
            <div id="splosna_napaka">
                <?php echo napaka("splosna");?>
            </div>
            <button id="submit" type ="submit"> Dodaj </button>
            <a id="preklici" href="uredi_kviz.php?id_kviza=<?php echo $_GET["id_kviza"]; ?>">Prekliči</a>
        </form>
</body>
</html>