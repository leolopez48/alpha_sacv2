let aside = document.querySelector(".aside");
let asideBtn = document.querySelector(".menu");
asideBtn.addEventListener("click", () => {
    aside.classList.toggle("active");
    if (
        aside.classList.contains("animate__animated", "animate__slideOutLeft")
    ) {
        aside.classList.remove("animate__animated", "animate__slideOutLeft");
    }
    aside.classList.add("animate__animated", "animate__slideInLeft");
});

let closeBtn = document.querySelector(".close-btn");
closeBtn.addEventListener("click", () => {
    if (aside.classList.contains("animate__slideInLeft")) {
        aside.classList.remove("animate__animated", "animate__slideInLeft");
    }
    aside.classList.add("animate__animated", "animate__slideOutLeft");

    setTimeout(() => {
        aside.classList.toggle("active");
    }, 500);
});
