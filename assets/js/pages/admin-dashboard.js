document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("reportDetailModal");
    const title = document.getElementById("modalTitle");
    const user = document.getElementById("modalUser");
    const time = document.getElementById("modalTime");
    const description = document.getElementById("modalDescription");

    const setActiveTab = (tabName) => {
        const reports = document.getElementById("view-reports");
        const feedback = document.getElementById("view-feedback");

        if (reports) {
            reports.style.display = tabName === "all" || tabName === "reports" ? "block" : "none";
        }

        if (feedback) {
            feedback.style.display = tabName === "all" || tabName === "feedback" ? "block" : "none";
        }

        document.querySelectorAll("[data-admin-tab]").forEach((button) => {
            button.classList.toggle("active", button.dataset.adminTab === tabName);
        });
    };

    document.querySelectorAll("[data-admin-tab]").forEach((button) => {
        button.addEventListener("click", () => setActiveTab(button.dataset.adminTab));
    });

    document.querySelectorAll("[data-report-open]").forEach((button) => {
        button.addEventListener("click", () => {
            if (!modal || !title || !user || !time || !description) {
                return;
            }

            const type = button.dataset.reportType || "Report";
            title.innerText = `${type === "Report" ? "🚩" : "💡"} ${type} Detail`;
            user.innerText = button.dataset.reportUser || "";
            time.innerText = button.dataset.reportTime || "";
            description.innerText = button.dataset.reportDescription || "";
            modal.style.display = "flex";
        });
    });

    document.querySelectorAll("[data-report-close]").forEach((button) => {
        button.addEventListener("click", () => {
            if (modal) {
                modal.style.display = "none";
            }
        });
    });

    if (modal) {
        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }
});

