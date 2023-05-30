function init() {
    const form = document.getElementById('form1');
    form.addEventListener('submit', event => validate(event));

}


function ValidateEmail(InputText) { // VALIDACE EMAILU
    var mailFormat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if(InputText.match(mailFormat))
    {
        return true;
    }
    else {
        return false;
    }
}

function validate(event) {

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const passwordrepeat = document.getElementById('passrepeat').value;
    const sex1 = document.getElementById('sex1');
    const sex2 = document.getElementById('sex2');
    const sex3 = document.getElementById('sex3');
    const ZS1 = document.getElementById('ZS1');
    const ZS2 = document.getElementById('ZS2');
    const pikomatsolver1 = document.getElementById('pikomatsolver1');
    const pikomatsolver2 = document.getElementById('pikomatsolver2');
    const emailAddr = document.getElementById('email').value;

    if (username.length < 5) { // VALIDACE JMENA
        event.preventDefault();
        alert("Zadej delší jméno!")

    } // username
    else {


        if (password.length < 7) { // HESLA
            event.preventDefault()
            alert("Hesla musí mít 8 nebo více znaků!")

        } // validace password

        if (password !== passwordrepeat) { // JESTLI SE SHODUJI
            event.preventDefault()
            alert("Hesla se neshodují!")

        } // validace password
    } // validace password
    if (sex1.checked === false && sex2.checked === false && sex3.checked === false) { // RADIO BUTTONY
        event.preventDefault();
        alert("Vyber pohlaví!")
    } // validace school

    if (ZS1.checked === false && ZS2.checked === false) { // RADIO BUTTONY
        event.preventDefault();
        alert("Vyber jestli studuješ na ZŠ!")
    } // validace zs

    if (pikomatsolver1.checked === false && pikomatsolver2.checked === false) { // RADIO BUTTONY
        event.preventDefault();
        alert("Vyber jestli řešíš Pikomat!")
    } //validace pikomat

    if(!ValidateEmail(emailAddr)) { // VOLA VALIDACI EMAILU
        event.preventDefault();
        alert("spatna adresa");
    }

}