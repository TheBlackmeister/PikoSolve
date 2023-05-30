function darkmodefuncsmol() {
    const btn = document.querySelector("#darkmodebtnsmol");
    const theme = document.querySelector("#theme-link");
    checkLS();
    btn.addEventListener("click", function () {
        // Swap out the URL for the different stylesheets
        if (theme.getAttribute("href") === "css/styles_index.css") { // TAHLE ZJISTUJE JAKA JE PROMENNA
            theme.href = "css/styles_index_dark.css";
            swapStylesheet('yes')
        } else {
            theme.href = "css/styles_index.css";
            swapStylesheet('no')
        }
    });
}

function swapStylesheet(yesno){ // MENI PROMENNOU
    localStorage.setItem('darktoggle', yesno);
}

function checkLS() { //CHECK LOCAL STORAGE FOR SAVED MODE
    let yesno = localStorage.getItem('darktoggle');
    const theme = document.querySelector("#theme-link");
    if (yesno === 'no') {
        theme.href = "css/styles_index.css";
    }
    else if (yesno === 'yes') {
        theme.href = "css/styles_index_dark.css";
    }
}