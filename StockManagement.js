function toggledropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.closest(".userprofile")) {
        document.getElementById("dropdown").classList.remove("show");
    }
}