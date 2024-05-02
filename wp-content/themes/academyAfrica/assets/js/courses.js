window.addEventListener("DOMContentLoaded", () => {
    const enrollBtn = document.querySelectorAll("#enroll-button");
    if (enrollBtn.length) {
        enrollBtn.forEach(btn => {
            const btnSubmit = btn.querySelector("input[type='submit'].learndash-button-free");
            if (btnSubmit) {
                btnSubmit.value = "Enroll Now";
                btnSubmit.classList.add("button", "secondary", "large");
            }
        });
    }

    setInterval(() => {
        const reShowQuestion = document.querySelector("input[name='reShowQuestion'].wpProQuiz_button_reShowQuestion");
        if (reShowQuestion) {
            reShowQuestion.value = "View Answers";
        }
    }, 1000)
});

