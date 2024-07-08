// toggle class active
const navbarnav = document.querySelector(".navbar-nav");
// ketika menu di klik
document.querySelector("#menu").onclick = () => {
  navbarnav.classList.toggle("active");
};

//  KLIK di luar sidebaar utk menghilangkan nav
const menu = document.querySelector("#menu");

document.addEventListener("click", function (e) {
  if (!menu.contains(e.target) && !navbarnav.contains(e.target)) {
    navbarnav.classList.remove("active");
  }
});
