document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("typeModal");
    const title = document.getElementById("modalTitle");
    const idInput = document.getElementById("type_id");
    const nameInput = document.getElementById("type_name");

    const closeModal = () => {
        if (modal) {
            modal.style.display = "none";
        }
    };

    document.querySelectorAll("[data-type-modal-open]").forEach((button) => {
        button.addEventListener("click", () => {
            if (!modal || !title || !idInput || !nameInput) {
                return;
            }

            const id = button.dataset.typeId || "";
            const name = button.dataset.typeName || "";

            idInput.value = id;
            nameInput.value = name;
            title.innerText = id ? "⚒️ Reforge Trial Type" : "⚒️ New Trial Entry";
            modal.style.display = "flex";
        });
    });

    document.querySelectorAll("[data-type-modal-close]").forEach((button) => {
        button.addEventListener("click", closeModal);
    });

    document.querySelectorAll("[data-type-delete]").forEach((button) => {
        button.addEventListener("click", () => {
            const id = button.dataset.typeDelete;
            if (window.confirm("Banish this trial type forever? This may break existing quests!")) {
                window.location.href = `/admin/delete-type.php?id=${id}`;
            }
        });
    });

    if (modal) {
        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
    }
});

