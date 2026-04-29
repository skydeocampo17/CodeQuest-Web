document.addEventListener("DOMContentLoaded", () => {
    const rulesModal = document.getElementById("rulesModal");
    const rulesLink = document.getElementById("rulesLink");
    const closeRulesButton = document.getElementById("closeModalBtn");
    const errorModal = document.getElementById("errorModal");
    const closeErrorButton = document.getElementById("closeErrorBtn");

    const openModal = (modal) => {
        if (!modal) {
            return;
        }

        modal.hidden = false;
        modal.setAttribute("aria-hidden", "false");
        modal.style.display = "flex";
    };

    const closeModal = (modal) => {
        if (!modal) {
            return;
        }

        modal.hidden = true;
        modal.setAttribute("aria-hidden", "true");
        modal.style.display = "none";
    };

    if (rulesLink && rulesModal) {
        rulesLink.addEventListener("click", (event) => {
            event.preventDefault();
            openModal(rulesModal);
        });
    }

    if (closeRulesButton && rulesModal) {
        closeRulesButton.addEventListener("click", () => {
            closeModal(rulesModal);
        });
    }

    if (closeErrorButton && errorModal) {
        closeErrorButton.addEventListener("click", () => {
            closeModal(errorModal);
        });
    }

    window.addEventListener("click", (event) => {
        if (event.target === rulesModal) {
            closeModal(rulesModal);
        }

        if (event.target === errorModal) {
            closeModal(errorModal);
        }
    });
});
