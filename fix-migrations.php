<?php
// Script para corrigir migrações pendentes que já existem no banco

$migrationsToMark = [
    '2024_01_20_144529_create_category_games_table',
    '2024_01_20_182155_add_vibra_to_games_keys_table',
    '2024_01_28_194456_add_vip_to_wallet_table', 
    '2024_01_28_231843_create_vip_users_table',
    '2024_01_29_102939_add_paid_to_limits_table',
    '2024_02_09_233936_add_worldslot_to_games_keys_table',
    '2024_02_10_191215_add_disable_spin_to_settings_table',
    '2024_02_17_210822_add_games2_to_games_keys_table',
    '2024_02_20_004853_add_sub_to_settings_table',
    '2024_02_26_144235_create_ggr_games_world_slots_table',
    '2024_03_24_172243_add_receita_to_affiliate_histories_table',
    '2024_04_06_201319_create_affiliate_logs',
    '2024_11_27_225343_create_mission_progress_table',
    '2024_11_29_110804_create_vips_table',
    '2025_01_29_120000_fix_gateway_column',
    '2025_07_29_104334_add_default_gateway_to_settings_table',
    '2025_07_29_150948_add_gateway_column_to_tables_fix',
    '2025_07_29_152322_fix_gateway_column_manually'
];

$conn = new mysqli('localhost', 'root', '', 'lucrativabet');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter o próximo batch
$result = $conn->query("SELECT MAX(batch) as max_batch FROM migrations");
$row = $result->fetch_assoc();
$nextBatch = ($row['max_batch'] ?? 0) + 1;

echo "Marcando migrações com batch $nextBatch...\n";

foreach ($migrationsToMark as $migration) {
    $sql = "INSERT IGNORE INTO migrations (migration, batch) VALUES ('$migration', $nextBatch)";
    if ($conn->query($sql) === TRUE) {
        echo "✓ $migration\n";
    } else {
        echo "✗ $migration: " . $conn->error . "\n";
    }
}

$conn->close();
echo "\nConcluído!\n";