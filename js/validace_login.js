function init() {
    const form = document.getElementById('login');
    form.addEventListener('submit', event => validate(event));

}

function validate(event) {

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    if (username.length < 5) { // JMENO
        event.preventDefault();
        alert("Zadej delší jméno!")

    } // username
    else {


        if (password.length < 8) { // HESLO MUSI BYT DELSI
            event.preventDefault()
            alert("Hesla má 8 nebo více znaků!")

        } // validace password
    } // validace password

}