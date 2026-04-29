/**
 * CODEQUEST GAME CONTROLLER
 * Handles Fullscreen and IFrame focus
 */

function toggleFullscreen() {
    const gameElem = document.getElementById("gameFrame");
    
    if (!document.fullscreenElement) {
        if (gameElem.requestFullscreen) {
            gameElem.requestFullscreen().catch(err => {
                console.error(`Fullscreen Error: ${err.message}`);
            });
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

// Ensure the game frame gets focus so the keyboard works immediately
window.addEventListener("load", () => {
    const iframe = document.getElementById("godotFrame");
    if (iframe) {
        iframe.focus();
    }
});