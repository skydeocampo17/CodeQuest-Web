<?php
/**
 * CODEQUEST DASHBOARD LOGIC - Updated with Default Languages
 */

require_once __DIR__ . '/../../data/database.php';

$is_guest = !isset($_SESSION['user_id']);

// Define the 4 Core Pillars of the Realm
$core_languages = [
    'C' => ['lang' => 'C', 'current_xp' => 0, 'level' => 1],
    'C#' => ['lang' => 'C#', 'current_xp' => 0, 'level' => 1],
    'JAVA' => ['lang' => 'JAVA', 'current_xp' => 0, 'level' => 1],
    'PHP' => ['lang' => 'PHP', 'current_xp' => 0, 'level' => 1]
];

if (!$is_guest) {
    $user_id = $_SESSION['user_id'];
    $adventurer_name = $_SESSION['username'] ?? 'Hero';

    try {
        $xp = (float) cq_get_user_total_xp($pdo, (int) $user_id);
        
        // Rank Logic
        if ($xp > 20000) $rank = "Elite Knight";
        elseif ($xp > 10000) $rank = "Squire";
        else $rank = "Wanderer";

        $db_stats = cq_get_user_mastery_rows($pdo, (int) $user_id);

        foreach ($db_stats as $row) {
            $lang_name = $row['lang'];
            if (isset($core_languages[$lang_name])) {
                $core_languages[$lang_name]['current_xp'] = $row['current_xp'];
                $core_languages[$lang_name]['level'] = floor($row['current_xp'] / 1000) + 1;
            }
        }
        
        // Set the final variable for the view
        $mastery_stats = $core_languages;

    } catch (PDOException $e) {
        $xp = 0; $rank = "Error"; $mastery_stats = $core_languages;
    }
} else {
    $adventurer_name = "Unknown Traveler";
    $xp = 0;
    $rank = "Wanderer";
    $mastery_stats = $core_languages;
}

// GLOBAL HALL OF HEROES
try {
    $top_heroes = cq_get_global_leaderboard_rows($pdo, 5);
} catch (PDOException $e) {
    $top_heroes = []; 
}
