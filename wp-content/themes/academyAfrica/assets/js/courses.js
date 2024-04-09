window.addEventListener("DOMContentLoaded", () => {
    const enrollBtn = document.querySelectorAll("#enroll-button");
    if(enrollBtn.length){
        enrollBtn.forEach(btn => {
        const btnSubmit = btn.querySelector("input[type='submit'].learndash-button-free");
        if(btnSubmit){
            btnSubmit.value = "Enroll Now";
            btnSubmit.classList.add("button", "secondary", "large");
        }
    });
    }

    // get button with name 'reShowQuestion' and class 'wpProQuiz_button_reShowQuestion'
    const reShowQuestion = document.querySelector("button[name='reShowQuestion'].wpProQuiz_button_reShowQuestion");
    if(reShowQuestion){
    //    console.log("reShowQuestion button found");
    //    TODO: update inner text to View Answers
    }
 });