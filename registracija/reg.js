
function preveri(input){
    napaka(input,"");
    let vnos = input.value.trim();
    let id = input.id;
    if(vnos.length == 0){
        napaka(input,"Obvezno izpolnite to polje");
    }
    else if(id == "potrdi_geslo" && vnos != document.getElementById("geslo").value.trim()){
        napaka(input,"Gesli se ne ujemata");
    }    
    else if(vnos.length > 30){
        napaka(input,"Vnešeni podatki naj ne presegajo 30 znakov");
    }
    else if(id == "uporabnisko_ime" || id == "geslo"){
        if(vnos.indexOf(" ") != -1){
            napaka(input, input.placeholder + " naj ne vsebuje presledkov");
        }
        else if(id=="geslo" && !geslo(input, vnos)){} 
        else if(vnos.length < 6 ){
            napaka(input, input.placeholder + " naj bo dolgo vsaj 6 znakov");  
        }   
    }    
}
    
function geslo(input, vnos){
    let male = false, velike = false, stevilke = false, znaki = false;
    for(let i=0; i<vnos.length; i++){
        let x = vnos.charAt(i);
        if(x.toUpperCase() != x.toLowerCase()){
            if(x == x.toUpperCase()){
                velike = true;
            }
            else{
                male = true;
            }
        }
        else if(x >='0' && x <='9'){
            stevilke = true;
        }
        else{
            znaki = true;
        }
    }
    if(!(male && velike && stevilke && znaki)){
        function vsebuje(pogoj){
            if(pogoj){
                return '<span style="color: #00c853;"> &#x2713';
            }
            return '<span> &#x2715';
        }
        let sp = "Geslo mora vsebovati:<br>";
        sp += vsebuje(male) + " male črke</span><br>";
        sp += vsebuje(velike) + " velike črke</span><br>";
        sp += vsebuje(stevilke) + " številke</span><br>";
        sp += vsebuje(znaki) + " znake</span>";
        napaka(input, sp);
        return false
    }
    return true;
}   