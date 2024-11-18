
document.addEventListener("DOMContentLoaded", function () {

    // Font size and background color settings
    const fontSizeInput = document.getElementById("fontSize");
    const bgColorInput = document.getElementById("bgColor");
    const mainContent = document.getElementById("mainContent");

    // Load settings from localStorage
    if (localStorage.getItem("fontSize")) {
        mainContent.style.fontSize = localStorage.getItem("fontSize") + "px";
        fontSizeInput.value = localStorage.getItem("fontSize");
    }
    if (localStorage.getItem("bgColor")) {
        document.body.style.backgroundColor = localStorage.getItem("bgColor");
        bgColorInput.value = localStorage.getItem("bgColor");
    }

    fontSizeInput.addEventListener("input", function () {
        mainContent.style.fontSize = fontSizeInput.value + "px";
        localStorage.setItem("fontSize", fontSizeInput.value);
    });

    bgColorInput.addEventListener("input", function () {
        document.body.style.backgroundColor = bgColorInput.value;
        localStorage.setItem("bgColor", bgColorInput.value);
    });

    // Display current date and time
    function updateDateTime() {
        const now = new Date();
        document.getElementById('date-time').textContent = now.toLocaleString();
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
});
