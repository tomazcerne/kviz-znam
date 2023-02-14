function potrditev(){
    let dovoli = true;
    let tab = document.getElementsByClassName("vnos");
    for(let i=0; i<tab.length; i++){
        let input = tab[i].firstElementChild;
        preveri(input);
        if(input.nextElementSibling.innerHTML != ""){
            dovoli = false;
        }
    }
    return dovoli;  
}
function napaka(input, txt){
    let x = input.nextElementSibling;
    x.innerHTML = txt;
}
function pokaziGeslo(box){
    let input = box.parentElement.parentElement.firstElementChild;
    if(box.checked == true){
        input.type = "text";
    }
    else{
        input.type = "password";   
    }
}


