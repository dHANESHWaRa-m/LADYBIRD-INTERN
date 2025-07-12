const hideToggle = document.querySelector(".hide-toggle");
const menuToggle = document.querySelector(".menu-toggle");
const navLinks = document.querySelector(".nav-links");

hideToggle.addEventListener("click", () => {
  navLinks.classList.add("hide");
  navLinks.classList.remove("show");
});

menuToggle.addEventListener("click", () => {
  navLinks.classList.remove("hide");
  navLinks.classList.toggle("show");
});
