<?php
    session_start();
    if(!isset($_SESSION["id_uporabnika"])){
        $_SESSION["nazaj"] = "kvizi.php";
        header("Location: prijava?nazaj=true");
        exit();
    }
    $iskanje = "";
    if(isset($_POST["isci"])){
        $iskanje = trim($_POST["isci"]);
    }
    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kvizi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="kvizi.css?verzija=7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        form{
            width: 90%;
            max-width: 700px;
            margin: 50px 0 20px 50%;
            transform: translate(-50%);
            text-align: center;
        }
        input[type=text]{
            margin: 10px 0;
            outline: none;
            border: solid gray 1px;
            border-radius: 5px;
            background-color: transparent;
            font-size: 18px;
            padding: 8px 8px 3px 8px;
            width: 70%;
            max-width: 400px;
            margin-right: 5px;
        }
        button{
            margin: 10px 0;
            padding: 8px 8px 3px 8px;
            outline: none;
            border: solid gray 1px;
            border-radius: 5px;
            background-color: transparent;
            font-size: 16px;
            width: 40px;
            color: #124fa0;
            
        }
        button:hover{
            background: #165ebd;
            color: white;
        }
        input[type=text]:focus{
            border: solid 1px #124fa0ee;
            border-radius: 5px;
            box-shadow: rgba(105, 170, 231, 0.5) 0px 2px 8px 0px;   
        }
        #nazaj{
            float: none;
            margin-left: 50%;
            transform: translate(-50%);
        }
        @media only screen and (max-width: 600px) {
            form{
                margin: 25px 0 0 50%;
            }
            input[type=text]{
                font-size: 16px;
            }
            button{
                font-size: 14.5px; 
            }
            #vsebina{
                margin-top: 5px;
                margin-bottom: 75px;
            }
            #nazaj{
                margin-top: 0;
            }
        }
    </style>
    <script>
        
        function seznam_kvizov(iskanje) {

            var poizvedba = new XMLHttpRequest();
            poizvedba.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("vsebina").innerHTML = this.responseText;
                }
            };
            poizvedba.open("POST", "seznam_kvizov.php");
            poizvedba.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            poizvedba.send("isci=" + iskanje); 
        }

    </script> 
</head>
<body>
       <form action="kvizi.php" method="post">
            <input type="text" name="isci" placeholder="išči" value="<?php echo $iskanje; ?>" autocomplete="off" onkeyup="seznam_kvizov(this.value.trim())">
            <button type="submit"> <i class="fas fa-search"></i> </button>
       </form>
       <div id="vsebina">
            <?php
                require("seznam_kvizov.php");
            ?>   
       </div>
       <div id="noga">
                <a id="nazaj" href="..">nazaj</a>
        </div> 
</body>
</html>