<?php
    $vrni = false;
    if(isset($_POST["id_vprasanja"])){
        require("../../podatki/podatki.php");
        $baza = new mysqli($p[0], $p[1], $p[2], $p[3]);
        if($baza->connect_error){
            header("Location: ../odjava.php");
            exit();
        }
        else{
            $pridobi_podatke = $baza->prepare("SELECT vprasanje, odgovor_A, odgovor_B, odgovor_C, odgovor_D FROM vprasanja WHERE id_vprasanja = ?");
            $pridobi_podatke->bind_param("i", $_POST["id_vprasanja"]);
            if($pridobi_podatke->execute()){
                $rezultat = $pridobi_podatke->get_result();
                if($rezultat->num_rows > 0){
                    $vrstica = $rezultat->fetch_assoc();

                    $vrni = '
                        <div id="vprasanje">
                            <span id="st"></span>
                            ' . $vrstica["vprasanje"] . '
                        </div>
                        <div id="odgovori">
                            <button class="odgovor" id="A" onclick="odgovor(this)"><span><span class="crka">A</span> ' . $vrstica["odgovor_A"] . '</span></button>
                            <button class="odgovor" id="B" onclick="odgovor(this)"><span><span class="crka">B</span> ' . $vrstica["odgovor_B"] . '</span></button>
                            <button class="odgovor" id="C" onclick="odgovor(this)"><span><span class="crka">C</span> ' . $vrstica["odgovor_C"] . '</span></button>
                            <button class="odgovor" id="D" onclick="odgovor(this)"><span><span class="crka">D</span> ' . $vrstica["odgovor_D"] . '</span></button>
                        </div>
                    ';

                }
            }
            $baza->close();
        }
    }
    echo $vrni;
?>