document.addEventListener("DOMContentLoaded", () => {
    const settingsModal = document.getElementById("settingsModal");
    const successModal = document.getElementById("updateSuccessModal");
    const state = document.getElementById("profilePageState");
    const shouldShowSuccess = state?.dataset.showProfileSuccess === "1";

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

    document.querySelectorAll("[data-settings-open]").forEach((button) => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            openModal(settingsModal);
        });
    });

    document.querySelectorAll("[data-settings-close]").forEach((button) => {
        button.addEventListener("click", () => {
            closeModal(settingsModal);
        });
    });

    document.querySelectorAll("[data-profile-success-close]").forEach((button) => {
        button.addEventListener("click", () => {
            closeModal(successModal);
        });
    });

    [settingsModal, successModal].forEach((modal) => {
        if (!modal) {
            return;
        }

        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    if (shouldShowSuccess && successModal) {
        openModal(successModal);
    }
});
