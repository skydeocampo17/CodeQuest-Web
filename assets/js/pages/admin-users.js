document.addEventListener("DOMContentLoaded", () => {
    const filterForm = document.getElementById("filterForm");
    const sortInput = document.getElementById("sort_order_input");
    const choiceModal = document.getElementById("choiceModal");
    const editUserModal = document.getElementById("editUserModal");
    const successModal = document.getElementById("successModal");

    const closeModal = (element) => {
        if (element) {
            element.style.display = "none";
        }
    };

    const openEditUserModal = (payload) => {
        document.getElementById("edit_user_id").value = payload.id || "";
        document.getElementById("edit_user_type_id").value = payload.typeId || "2";
        document.getElementById("edit_username").value = payload.name || "";
        if (editUserModal) {
            editUserModal.style.display = "flex";
        }
    };

    const openUserBriefing = (row) => {
        const payload = {
            id: row.dataset.userId || "",
            name: row.dataset.userName || "",
            email: row.dataset.userEmail || "",
            role: row.dataset.userRoleText || "",
            typeId: row.dataset.userTypeId || "2",
            date: row.dataset.userCreatedAt || "",
        };

        document.getElementById("brief_id").innerText = payload.id;
        document.getElementById("brief_name").innerText = payload.name;
        document.getElementById("brief_email").innerText = payload.email;
        document.getElementById("brief_role").innerText = payload.role;
        document.getElementById("brief_date").innerText = new Date(payload.date).toLocaleDateString();

        document.getElementById("brief_edit_btn").onclick = () => {
            closeModal(choiceModal);
            openEditUserModal(payload);
        };

        if (choiceModal) {
            choiceModal.style.display = "flex";
        }
    };

    if (filterForm) {
        filterForm.querySelectorAll("input:not([type='hidden']), select").forEach((element) => {
            element.addEventListener("input", () => {
                window.clearTimeout(window.searchTimer);
                window.searchTimer = window.setTimeout(() => filterForm.submit(), 500);
            });
        });
    }

    document.querySelectorAll("[data-toggle-sort]").forEach((button) => {
        button.addEventListener("click", () => {
            if (!filterForm || !sortInput) {
                return;
            }

            sortInput.value = sortInput.value === "ASC" ? "DESC" : "ASC";
            filterForm.submit();
        });
    });

    document.querySelectorAll("[data-open-user-briefing]").forEach((row) => {
        row.addEventListener("click", () => openUserBriefing(row));
    });

    document.querySelectorAll("[data-open-user-edit]").forEach((button) => {
        button.addEventListener("click", (event) => {
            event.stopPropagation();
            openEditUserModal({
                id: button.dataset.userId || "",
                name: button.dataset.userName || "",
                typeId: button.dataset.userTypeId || "2",
            });
        });
    });

    document.querySelectorAll("[data-modal-close]").forEach((button) => {
        button.addEventListener("click", () => {
            const target = button.dataset.modalClose;
            if (target === "successModal") {
                closeModal(successModal);
                const url = new URL(window.location.href);
                url.searchParams.delete("status");
                url.searchParams.delete("id");
                window.history.replaceState({}, document.title, url);
                return;
            }

            closeModal(document.getElementById(target));
        });
    });

    document.querySelectorAll(".admin-modal-overlay").forEach((overlay) => {
        overlay.addEventListener("click", (event) => {
            if (event.target === overlay) {
                closeModal(overlay);
            }
        });
    });

    const status = new URLSearchParams(window.location.search).get("status");
    if (status === "updated" && successModal) {
        successModal.style.display = "flex";
    }
});

