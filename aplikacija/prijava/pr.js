function preveri(input){
    napaka(input,"");
    let vnos = input.value.trim();
    if(vnos.length == 0){
        napaka(input,"Obvezno izpolnite to polje");
    }    
}
