function init() {
    const form = document.getElementById('newdocform');
    form.addEventListener('submit', event => validate(event));

}

function validate(event) {

    const nadpis = document.getElementById('nadpis').value;
    const freeform = document.getElementById('freeform').value;

    if (nadpis.length < 5) { // DELKA NADPISU
        event.preventDefault();
        alert("Zadej delší nadpis!")

    } // nadpis
    else {


        if (freeform.length > 1000) { // KRATSI NEZ 1000 MUSI BYT OBSAH
            event.preventDefault()
            alert("Délka popisu je limitována na 1000 znaků")

        } // validace popis
    } // validace popis

}