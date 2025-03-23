// Accordion
let acc = document.getElementsByClassName("easyVerein_accordion");
let i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener(
        "click", function () {
            let panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        }
    );
}