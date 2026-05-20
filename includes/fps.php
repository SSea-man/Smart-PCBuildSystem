<?php
/**
 
 */

function estimate_fps(int $cpu_id, int $gpu_id, string $game_slug): ?array {
    $cpu  = db_row('SELECT benchmark_score FROM component WHERE component_id=?', [$cpu_id]);
    $gpu  = db_row('SELECT benchmark_score FROM component WHERE component_id=?', [$gpu_id]);
    $game = db_row('SELECT * FROM fps_profiles WHERE game_slug=?', [$game_slug]);
    if (!$cpu || !$gpu || !$game) return null;
    return _calc_fps((float)$cpu['benchmark_score'], (float)$gpu['benchmark_score'], $game);
}

function estimate_fps_from_rows(array $cpu_row, array $gpu_row, array $game_row): array {
    return _calc_fps(
        (float)($cpu_row['benchmark_score'] ?? 0),
        (float)($gpu_row['benchmark_score'] ?? 0),
        $game_row
    );
}

function _calc_fps(float $cpu_s, float $gpu_s, array $game): array {
    $factor = (float)($game['difficulty_factor'] ?? 1);
    $comp   = ($gpu_s * 0.70 + $cpu_s * 0.30);
    $base   = $factor > 0 ? $comp / $factor : $comp;
    return [
        'min'        => (int)max(1, floor($base * 0.90)),
        'max'        => (int)ceil($base * 1.10),
        'resolution' => $game['resolution'] ?? '1080p',
        'quality'    => $game['quality']    ?? 'Medium',
    ];
}

function get_game_list(): array {
    return db_query('SELECT game_slug, game_name FROM fps_profiles ORDER BY game_name');
}
