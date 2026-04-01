// ===== THEME TOGGLE =====
// Apply theme immediately before paint to prevent flash
(function () {
    var saved = localStorage.getItem("theme");
    if (saved === "dark") {
        document.documentElement.classList.add("dark-theme");
        document.body && document.body.classList.add("dark-theme");
    }
})();

document.addEventListener("DOMContentLoaded", function () {
    // Re-apply in case body wasn't ready during IIFE
    var saved = localStorage.getItem("theme");
    if (saved === "dark") {
        document.body.classList.add("dark-theme");
    }

    var btn = document.getElementById("theme-toggle");
    if (!btn) return;

    // Prevent double-binding
    if (btn.dataset.bound) return;
    btn.dataset.bound = "1";

    function updateIcon() {
        var isDark = document.body.classList.contains("dark-theme");
        btn.textContent = isDark ? "☀️" : "🌙";
    }

    updateIcon();

    btn.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var isDark = document.body.classList.contains("dark-theme");
        if (isDark) {
            document.body.classList.remove("dark-theme");
            document.documentElement.classList.remove("dark-theme");
            localStorage.setItem("theme", "light");
        } else {
            document.body.classList.add("dark-theme");
            document.documentElement.classList.add("dark-theme");
            localStorage.setItem("theme", "dark");
        }
        updateIcon();
    });
});
