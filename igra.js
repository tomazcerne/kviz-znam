
var st_pravilnih = 0;
var joker_odgovor = [false, false];
var joker_aktiven = false;

function naprej() {
    trenutno_vprasanje++;
    if(trenutno_vprasanje == vprasanja.length){
        window.onbeforeunload = null;
        document.getElementById("polje").style.display = "none";
        document.getElementById("konec").style.display = "block";
        document.getElementById("pravilnih").innerHTML = st_pravilnih;
        document.getElementById("skupno").innerHTML = vprasanja.length;
        document.getElementById("uspesnost").innerHTML = Math.round((100.0*st_pravilnih)/vprasanja.length) + "%";

    }
    else{
        let poizvedba = new XMLHttpRequest();
        poizvedba.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let r = document.getElementsByClassName("rezultat")[trenutno_vprasanje];
                if(this.responseText != false){
                    document.getElementById("prikaz").innerHTML = this.responseText;
                    document.getElementById("st").innerHTML = trenutno_vprasanje + 1 + ".";
                    document.getElementById("pomoci").style.display = "flex";
                    document.getElementById("nadaljuj").style.display = "none";
                    r.style.color = "white";
                    r.style.background = "#124fa0";
                }
                else{
                    napaka();
                    r.style.border = "solid red 2px";
                }
                  
            }
        };
        poizvedba.open("POST", "poizvedbe/pridobi_vprasanje.php");
        poizvedba.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        poizvedba.send("id_vprasanja=" + vprasanja[trenutno_vprasanje] );
    } 
}

function napaka(){
    document.getElementById("pomoci").style.display = "none";
    document.getElementById("nadaljuj").style.display = "flex";
    document.getElementById("prikaz").innerHTML = '<div id="napaka">Prišlo je do nepričakovane napake. Najverjetneje je bilo vprašanje izbrisano. Prosimo, nadaljujte na naslednja vprašanja!</div>';
}

function pravilen(){
    let tmp_pr = false;
    let poizvedba = new XMLHttpRequest();
    poizvedba.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            tmp_pr = this.responseText;
        }
    };
    poizvedba.open("POST", "poizvedbe/pravilen.php", false);
    poizvedba.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    poizvedba.send("id_vprasanja=" + vprasanja[trenutno_vprasanje]);
    return tmp_pr;
}

function odgovor(element){
    if(!joker_aktiven){

        let izbira = element.id;
        let resitev = pravilen();
        for(let i=0; i<4; i++){
            document.getElementsByClassName("odgovor")[i].disabled = true;
        }
        document.getElementById("pomoci").style.display = "none";
        document.getElementById("nadaljuj").style.display = "flex";
        
        if(resitev == false){
            napaka();
        }
        else{
            let pravilno = (izbira==resitev);
            let r = document.getElementsByClassName("rezultat")[trenutno_vprasanje];
            if(pravilno){
                element.style.background = "#66ff66";
                element.style.color = "black";
                r.style.color = "black";
                r.style.background = "#66ff66";
                st_pravilnih++;   
            }
            else{
                element.style.background = "red";
                document.getElementById(resitev).style.background = "#66ff66";
                document.getElementById(resitev).style.color = "black";
                r.style.color = "white";
                r.style.background = "red";
            }
            r.style.paddingTop = "6px";
            r.style.border = "transparent";
            zabelezi_odgovor(izbira, pravilno);
        } 
    }
    else{
        joker_izbira(element);
    }
    
}

function zabelezi_odgovor(izbira, pravilno){
    let poizvedba = new XMLHttpRequest();
    poizvedba.open("POST", "poizvedbe/zabelezi_odgovor.php");
    poizvedba.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    poizvedba.send("id_vprasanja=" + vprasanja[trenutno_vprasanje] + "&id_rezultata=" + id_rezultata + "&izbira=" + izbira + "&pravilno=" + pravilno );
}

function nakljucno(min, max){
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function polovicka(){
    let p = document.getElementById("polovicka");
    p.disabled = true;
    p.style.display = "none";
    for(let i=0; i<2; i++){
        let el = document.getElementsByClassName("odgovor")[nakljucno(0,3)];
        if(el.disabled == true || el.id == pravilen()){
            i--;
            continue;
        }
        else{
            el.disabled = true;
            el.firstChild.style.visibility = "hidden";
            el.style.background = "#124fa0";
        }
    }
}


function joker(){
    joker_aktiven = true;
    document.getElementById("sporocilo").style.display = "block";
    document.getElementById("pomoci").style.display = "none";
    if(joker_odgovor[0] != false && joker_odgovor[1] != false){
        joker_aktiven = false;
        let pr = pravilen();
        while(true){
            let el = joker_odgovor[nakljucno(0,1)];
            if(el.id==pr){
                continue;
            }
            else{
                el.firstChild.style.visibility = "hidden";
                for(let i=0; i<2; i++){
                    joker_odgovor[i].disabled = false;
                    joker_odgovor[i].style = "";
                }
                el.disabled = true;
                el.style.background = "#124fa0";

                let j = document.getElementById("joker");
                j.disabled = true;
                j.style.display = "none";
                document.getElementById("pomoci").style.display = "flex";
                document.getElementById("sporocilo").style.display = "none";

                break;
            }
        }

    }
}

function joker_izbira(element){
    for(let i=0; i<2; i++){
        if(joker_odgovor[i]==false){
            joker_odgovor[i] = element;
            element.disabled = true;
            element.style.background = "#1d78f0";
            if(i==0){
                break;
            }
            else{
                setTimeout(() => {
                    joker();
                }, 500);
            }
        }    
    }    
}

function glas_ljudstva(){
    let g = document.getElementById("glas_ljudstva");
    g.disabled = true;
    g.style.display = "none";
    document.getElementById("polje").style.display = "none";
    document.getElementById("polje_glas_ljudstva").style.display = "block";

    let poizvedba = new XMLHttpRequest();
    poizvedba.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let abcd = this.responseText.split(",");
            let n = 0;
            for(let i=0; i<4; i++){
                abcd[i] = parseInt(abcd[i]);
                n += abcd[i];
            }
            for(let i=0; i<4; i++){
                let vrstica = document.getElementsByClassName("procent")[i];
                let odstotek  = Math.round((100.0*abcd[i])/n);
                vrstica.style.width = odstotek + "%";
                vrstica.innerHTML = odstotek + "%";
            }
        }
    };
    poizvedba.open("POST", "poizvedbe/pridobi_rezultate.php");
    poizvedba.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    poizvedba.send("id_vprasanja=" + vprasanja[trenutno_vprasanje]);

}

function zapri_glas_ljudstva(){
    document.getElementById("polje_glas_ljudstva").style.display = "none";
    document.getElementById("polje").style.display = "block";
}
