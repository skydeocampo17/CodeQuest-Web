/**
 * CODEQUEST: Kingdom Chronicles Feed Script
 */

let currentEventIndex = 0;
let isLoading = false;
let allEventsLoaded = false;
let currentLangFilter = 'ALL';

const feedContainer = document.getElementById("quest-feed");
const loadingTrigger = document.getElementById("loading-trigger");

/**
 * ✅ 1. The Intersection Observer (The Watchman)
 */
const scrollObserver = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting && !isLoading && !allEventsLoaded) {
        loadNextSocialEvent();
    }
}, { threshold: 0.1 });

if (loadingTrigger) {
    scrollObserver.observe(loadingTrigger);
}

/**
 * ✅ 2. Filter Function
 */
function filterFeed(lang, btnElement) {
    if (currentLangFilter === lang || isLoading) return;

    currentLangFilter = lang;
    currentEventIndex = 0;
    allEventsLoaded = false;
    feedContainer.innerHTML = "";
    loadingTrigger.innerText = "Summoning more activities...";

    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    if(btnElement) btnElement.classList.add('active');

    loadNextSocialEvent();
}

window.filterFeed = filterFeed;

/**
 * ✅ 3. Data Fetching (The Royal Messenger)
 * Now marked as 'async' to allow 'await'
 */
async function loadNextSocialEvent() {
    if (isLoading || allEventsLoaded) return;
    isLoading = true;

    // Build the query string correctly
    const params = new URLSearchParams({
        offset: currentEventIndex,
        lang: currentLangFilter
    });

    // Path Fix: Ensure absolute path for Laragon/Subfolder
    const fetchUrl = `/logic/ajax/fetch_social_feed.php?${params.toString()}`;

    try {
        const response = await fetch(fetchUrl);
        
        if (!response.ok) throw new Error("The royal scrolls are missing!");
        
        const events = await response.json();

        if (!Array.isArray(events) || events.length === 0) {
            allEventsLoaded = true;
            loadingTrigger.innerText = "The kingdom is quiet... for now.";
            return;
        }

        events.forEach(event => {
            const card = createActivityCard(event);
            feedContainer.appendChild(card);
        });

        currentEventIndex += events.length;
        isLoading = false;

        // Auto-summon if screen isn't full
        if (document.documentElement.scrollHeight <= window.innerHeight) {
            loadNextSocialEvent();
        }
    } catch (err) {
        console.error("Feed error:", err);
        loadingTrigger.innerText = "The royal messenger got lost...";
        isLoading = false;
    }
}

/**
 * ✅ 4. Card Creation (The Scribe)
 */
function createActivityCard(event) {
    const card = document.createElement("div");
    
    // Normalize names for styling
    let langName = event.language ? event.language.toUpperCase() : "SYSTEM";
    let langClass = "card-system";
    let langColor = "#888";
    
    if (langName.includes("JAVA")) { langClass = "card-java"; langColor = "#f89820"; }
    else if (langName.includes("PHP")) { langClass = "card-php"; langColor = "#777bb4"; }
    else if (langName.includes("C#")) { langClass = "card-csharp"; langColor = "#178600"; }
    else if (langName.includes("C")) { langClass = "card-c"; langColor = "#a8b9cc"; }

    card.className = `feed-card ${langClass}`;

    // Format time (Handle SQL format)
    const timeString = new Date(event.last_updated).toLocaleString('en-US', { 
        month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true 
    });

    card.innerHTML = `
        <div class="event-icon" style="font-size: 2.5rem; min-width: 60px; text-align: center;">⚔️</div>
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center;">
                    <span class="lang-tag" style="background:${langColor}; color:white; font-family:'VT323'; padding:2px 6px; border-radius:4px; font-size:0.8rem;">${langName}</span>
                </div>
                <div style="font-family: 'VT323'; color: #888; font-size: 0.9rem;">${timeString}</div>
            </div>
            <h3 style="font-family:'Chelsea Market'; margin:5px 0;">
                <span style="color:var(--ts-red);">${event.username}</span>
                achieved greatness!
            </h3>
            <p style="font-family:'VT323'; font-size:1.2rem; color:#444; margin:0;">
                Earned ${event.score} XP in the latest trials.
            </p>
        </div>
    `;
    return card;
}

window.addEventListener("load", loadNextSocialEvent);
