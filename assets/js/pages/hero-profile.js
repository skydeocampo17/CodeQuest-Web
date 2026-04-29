document.addEventListener("DOMContentLoaded", () => {
    const settingsModal = document.getElementById("settingsModal");
    const successModal = document.getElementById("updateSuccessModal");
    const state = document.getElementById("profilePageState");
    const shouldShowSuccess = state?.dataset.showProfileSuccess === "1";

    document.querySelectorAll("[data-settings-open]").forEach((button) => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            if (settingsModal) {
                settingsModal.style.display = "flex";
            }
        });
    });

    document.querySelectorAll("[data-settings-close]").forEach((button) => {
        button.addEventListener("click", () => {
            if (settingsModal) {
                settingsModal.style.display = "none";
            }
        });
    });

    document.querySelectorAll("[data-profile-success-close]").forEach((button) => {
        button.addEventListener("click", () => {
            if (successModal) {
                successModal.style.display = "none";
            }
        });
    });

    [settingsModal, successModal].forEach((modal) => {
        if (!modal) {
            return;
        }

        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    });

    if (shouldShowSuccess && successModal) {
        successModal.style.display = "flex";
    }
});
