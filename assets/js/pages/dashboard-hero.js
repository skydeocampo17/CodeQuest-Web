document.addEventListener("DOMContentLoaded", () => {
    const successAlert = document.querySelector(".quest-alert.success.fade-out-auto");

    document.querySelectorAll("[data-feed-filter]").forEach((button) => {
        button.addEventListener("click", () => {
            if (typeof window.filterFeed === "function") {
                window.filterFeed(button.dataset.feedFilter, button);
            }
        });
    });

    if (!successAlert) {
        return;
    }

    window.setTimeout(() => {
        successAlert.style.transition = "all 0.8s ease";
        successAlert.style.opacity = "0";
        successAlert.style.transform = "translateY(-20px)";
        window.setTimeout(() => successAlert.remove(), 800);
    }, 5000);
});
