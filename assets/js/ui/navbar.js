document.addEventListener("DOMContentLoaded", () => {
    const burger = document.getElementById("navBurger");
    const menu = document.getElementById("navMenu");
    const logoutModal = document.getElementById("logoutConfirmModal");
    const openLogoutButton = document.querySelector("[data-logout-open]");
    const closeLogoutButton = document.querySelector("[data-logout-close]");

    const openModal = (modal) => {
        if (!modal) {
            return;
        }

        modal.hidden = false;
        modal.style.display = "flex";
        modal.setAttribute("aria-hidden", "false");
    };

    const closeModal = (modal) => {
        if (!modal) {
            return;
        }

        modal.hidden = true;
        modal.style.display = "none";
        modal.setAttribute("aria-hidden", "true");
    };

    if (burger && menu) {
        burger.addEventListener("click", () => {
            const isOpen = menu.classList.toggle("active");
            burger.classList.toggle("active", isOpen);
            burger.setAttribute("aria-expanded", isOpen ? "true" : "false");
        });

        document.addEventListener("click", (event) => {
            if (!burger.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.remove("active");
                burger.classList.remove("active");
                burger.setAttribute("aria-expanded", "false");
            }
        });
    }

    if (openLogoutButton && logoutModal) {
        openLogoutButton.addEventListener("click", (event) => {
            event.preventDefault();
            openModal(logoutModal);
        });
    }

    if (closeLogoutButton && logoutModal) {
        closeLogoutButton.addEventListener("click", () => {
            closeModal(logoutModal);
        });
    }

    if (logoutModal) {
        logoutModal.addEventListener("click", (event) => {
            if (event.target === logoutModal) {
                closeModal(logoutModal);
            }
        });
    }
});
