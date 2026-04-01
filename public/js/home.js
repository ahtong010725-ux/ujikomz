function toggleDropdown() {
    const menu = document.getElementById("dropdownMenu");
    menu.style.display = menu.style.display === "flex" ? "none" : "flex";
}

window.addEventListener("click", function (e) {
    const menu = document.getElementById("dropdownMenu");
    const avatar = document.querySelector(".user-avatar");
    if (avatar && menu && !avatar.contains(e.target)) {
        menu.style.display = "none";
    }
});
