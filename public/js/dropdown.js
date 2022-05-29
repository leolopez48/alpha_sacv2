//Dropdown-list
let dropdown = document.querySelector(".dropdown-list");
let dropdownBtn = document.querySelector(".user-btn");
dropdownBtn.addEventListener("click", () => {
    dropdown.classList.toggle("active");
    if (dropdown.classList.contains("animate__animated", "animate__fadeOut")) {
        dropdown.classList.remove("animate__animated", "animate__fadeOut");
    }
    dropdown.classList.add("animate__animated", "animate__fadeIn");
});
