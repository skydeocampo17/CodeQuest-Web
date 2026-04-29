document.addEventListener("DOMContentLoaded", () => {
    const rulesModal = document.getElementById("rulesModal");
    const rulesLink = document.getElementById("rulesLink");
    const closeRulesButton = document.getElementById("closeModalBtn");
    const errorModal = document.getElementById("errorModal");
    const closeErrorButton = document.getElementById("closeErrorBtn");

    if (rulesLink && rulesModal) {
        rulesLink.addEventListener("click", (event) => {
            event.preventDefault();
            rulesModal.style.display = "flex";
        });
    }

    if (closeRulesButton && rulesModal) {
        closeRulesButton.addEventListener("click", () => {
            rulesModal.style.display = "none";
        });
    }

    if (closeErrorButton && errorModal) {
        closeErrorButton.addEventListener("click", () => {
            errorModal.style.display = "none";
        });
    }

    window.addEventListener("click", (event) => {
        if (event.target === rulesModal) {
            rulesModal.style.display = "none";
        }

        if (event.target === errorModal) {
            errorModal.style.display = "none";
        }
    });
});

