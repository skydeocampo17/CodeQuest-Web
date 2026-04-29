document.addEventListener("DOMContentLoaded", () => {
    const filterForm = document.getElementById("filterForm");
    const sortInput = document.getElementById("sort_order_input");
    const choiceModal = document.getElementById("choiceModal");
    const editModal = document.getElementById("editModal");
    const successModal = document.getElementById("successModal");
    const quizTypeSelect = document.getElementById("modal_quiz_type");
    const wrongAnswersSection = document.getElementById("wrong_answers_section");
    const correctInput = document.getElementById("modal_correct_input");
    const correctTrueFalse = document.getElementById("modal_correct_tf");

    const updateQuestUI = () => {
        if (!quizTypeSelect || !wrongAnswersSection || !correctInput || !correctTrueFalse) {
            return;
        }

        const quizId = quizTypeSelect.value;

        if (quizId === "1") {
            wrongAnswersSection.classList.remove("hidden");
            correctInput.classList.remove("hidden");
            correctTrueFalse.classList.add("hidden");
        } else if (quizId === "2") {
            wrongAnswersSection.classList.add("hidden");
            correctInput.classList.add("hidden");
            correctTrueFalse.classList.remove("hidden");
        } else {
            wrongAnswersSection.classList.add("hidden");
            correctInput.classList.remove("hidden");
            correctTrueFalse.classList.add("hidden");
        }
    };

    const closeModal = (element) => {
        if (element) {
            element.style.display = "none";
        }
    };

    const openEditModal = (payload) => {
        document.getElementById("modal_title").innerText = "⚒️ Reforge Details";
        document.getElementById("modal_id").value = payload.id || "";
        document.getElementById("modal_language").value = payload.langId || "";
        document.getElementById("modal_text").value = payload.text || "";
        document.getElementById("modal_quiz_type").value = payload.quizId || "1";
        document.getElementById("modal_level").value = payload.levelId || "1";

        if (String(payload.quizId) === "2") {
            document.getElementById("modal_correct_tf").value = payload.correct || "True";
            document.getElementById("modal_correct_input").value = "";
        } else {
            document.getElementById("modal_correct_input").value = payload.correct || "";
        }

        document.getElementById("modal_w1").value = payload.w1 === "N/A" ? "" : (payload.w1 || "");
        document.getElementById("modal_w2").value = payload.w2 === "N/A" ? "" : (payload.w2 || "");
        document.getElementById("modal_w3").value = payload.w3 === "N/A" ? "" : (payload.w3 || "");

        updateQuestUI();

        if (editModal) {
            editModal.style.display = "flex";
        }
    };

    const openChoiceModal = (row) => {
        const payload = {
            id: row.dataset.questId,
            text: row.dataset.questText || "",
            correct: row.dataset.questCorrect || "",
            w1: row.dataset.questWrong1 || "",
            w2: row.dataset.questWrong2 || "",
            w3: row.dataset.questWrong3 || "",
            quizId: row.dataset.questQuizId || "1",
            langId: row.dataset.questLanguageId || "1",
            levelId: row.dataset.questLevelId || "1",
        };

        document.getElementById("choice_quest_id").innerText = payload.id;
        document.getElementById("info_question_text").innerText = payload.text;
        document.getElementById("info_correct").innerText = `✔️ ${payload.correct}`;

        const wrongList = document.getElementById("info_wrong_list");
        const wrongWrapper = document.getElementById("info_wrong_wrapper");
        wrongList.innerHTML = "";

        if (String(payload.quizId) === "1") {
            wrongWrapper.classList.remove("hidden");
            [payload.w1, payload.w2, payload.w3]
                .filter((value) => value && value !== "N/A")
                .forEach((trap) => {
                    const li = document.createElement("li");
                    li.innerText = `❌ ${trap}`;
                    wrongList.appendChild(li);
                });
        } else {
            wrongWrapper.classList.add("hidden");
        }

        document.getElementById("choice_edit_btn").onclick = () => {
            closeModal(choiceModal);
            openEditModal(payload);
        };

        document.getElementById("choice_delete_btn").onclick = () => {
            closeModal(choiceModal);
            const id = payload.id;
            if (window.confirm(`Are you sure you wish to banish Quest #${id}?`)) {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "/admin/delete-quest.php";
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "id";
                input.value = id;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
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

    document.querySelectorAll("[data-open-choice]").forEach((row) => {
        row.addEventListener("click", () => openChoiceModal(row));
    });

    document.querySelectorAll("[data-open-new-quest]").forEach((button) => {
        button.addEventListener("click", () => {
            document.getElementById("modal_title").innerText = "⚒️ New Quest Entry";
            document.getElementById("modal_id").value = "";
            document.getElementById("modal_text").value = "";
            document.getElementById("modal_language").selectedIndex = 0;
            document.getElementById("modal_quiz_type").value = "1";
            document.getElementById("modal_level").selectedIndex = 0;
            document.getElementById("modal_correct_input").value = "";
            document.getElementById("modal_correct_tf").value = "True";
            document.getElementById("modal_w1").value = "";
            document.getElementById("modal_w2").value = "";
            document.getElementById("modal_w3").value = "";
            updateQuestUI();
            if (editModal) {
                editModal.style.display = "flex";
            }
        });
    });

    document.querySelectorAll("[data-quest-delete]").forEach((button) => {
        button.addEventListener("click", (event) => {
            event.stopPropagation();
            const id = button.dataset.questDelete;
            if (window.confirm(`Are you sure you wish to banish Quest #${id}?`)) {
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "/admin/delete-quest.php";
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "id";
                input.value = id || "";
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    document.querySelectorAll("[data-modal-close]").forEach((button) => {
        button.addEventListener("click", () => {
            const target = button.dataset.modalClose;
            if (target === "successModal") {
                closeModal(successModal);
                const url = new URL(window.location.href);
                url.searchParams.delete("status");
                window.history.replaceState({}, document.title, url);
                return;
            }

            closeModal(document.getElementById(target));
        });
    });

    if (quizTypeSelect) {
        quizTypeSelect.addEventListener("change", updateQuestUI);
        updateQuestUI();
    }

    document.querySelectorAll(".admin-modal-overlay").forEach((overlay) => {
        overlay.addEventListener("click", (event) => {
            if (event.target === overlay) {
                closeModal(overlay);
            }
        });
    });

    const status = new URLSearchParams(window.location.search).get("status");
    if (successModal && status) {
        const message = document.getElementById("success_message");
        if (status === "deleted" && message) {
            message.innerText = "Quest banished successfully.";
        }
        successModal.style.display = "flex";
    }
});
