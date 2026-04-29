document.addEventListener("DOMContentLoaded", () => {
    const mobileNotice = document.getElementById("mobile-notice");
    const iframe = document.getElementById("godotFrame");

    if (iframe) {
        iframe.focus();
    }

    if (!mobileNotice) {
        return;
    }

    const userAgent = navigator.userAgent;
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent);
    const isAndroid = /Android/i.test(userAgent);
    const noticeText = mobileNotice.querySelector("[data-mobile-notice-text]");

    if (!isMobile) {
        return;
    }

    mobileNotice.style.display = "block";

    if (isAndroid && noticeText) {
        noticeText.textContent = "CodeQuest is optimized for Android. Download it on Google Play for the smoothest performance.";
    }
});

