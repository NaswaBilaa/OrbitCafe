import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
document.addEventListener("DOMContentLoaded", function () {
    const buttonMenu = document.getElementById("button-menu");
    const menuItems = document.querySelectorAll(".showMenu");

    if (buttonMenu) {
        buttonMenu.addEventListener("click", function () {
            menuItems.forEach((item) => {
                item.classList.toggle("hidden");
            });

            const text = buttonMenu.querySelector("p");
            if (text && text.innerText === "Show Menu") {
                text.innerText = "Hide Menu";
            } else if (text) {
                text.innerText = "Show Menu";
            }
        });
    }
});