<?php
/**
 * CODEQUEST HERO PROFILE LOGIC
 * Fetches stats for a specific adventurer based on the URL parameter.
 */

require_once __DIR__ . '/../../data/database.php';

// 1. Get the target hero's name from the URL (prepared by the Router)
$target_username = $_GET['target_hero'] ?? null;

if (!$target_username) {
    header("Location: /heroes");
    exit();
}

try {
    // 2. Seek the Hero in the Kingdom's Registry
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$target_username]);
    $profile_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile_user) {
        // Hero not found! Send them back to the Hall of Heroes with a message.
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['error'] = "The hero '$target_username' does not exist in these lands.";
        header("Location: /heroes");
        exit();
    }

    $profile_id = $profile_user['id'];
    $profile_name = $profile_user['username'];

    // 3. Fetch Total Strength (XP)
    $profile_xp = (float) cq_get_user_total_xp($pdo, (int) $profile_id);

    // 4. Calculate Rank (Using your old dashboard's specific rank tiers)
    if ($profile_xp > 15000) $profile_rank = "Legendary Coder";
    elseif ($profile_xp > 10000) $profile_rank = "Elite Knight";
    else $profile_rank = "Recruit";

    // 5. Fetch Language Mastery & Merge with the 4 Core Languages
    $core_languages = [
        'C' => ['lang' => 'C', 'current_xp' => 0, 'level' => 1],
        'C#' => ['lang' => 'C#', 'current_xp' => 0, 'level' => 1],
        'JAVA' => ['lang' => 'JAVA', 'current_xp' => 0, 'level' => 1],
        'PHP' => ['lang' => 'PHP', 'current_xp' => 0, 'level' => 1]
    ];

    $db_stats = cq_get_user_mastery_rows($pdo, (int) $profile_id);

    // Fill the progress bars with actual data if the hero has played
    foreach ($db_stats as $row) {
        $lang_name = strtoupper($row['lang']);
        // Normalize C# naming just in case
        if ($lang_name === 'CSHARP') $lang_name = 'C#'; 
        
        if (isset($core_languages[$lang_name])) {
            $core_languages[$lang_name]['current_xp'] = $row['current_xp'];
            $core_languages[$lang_name]['level'] = floor($row['current_xp'] / 1000) + 1;
        }
    }
    
    $profile_mastery = $core_languages;

} catch (PDOException $e) {
    die("The Royal Archives are sealed. Database Error: " . $e->getMessage());
}
?>
