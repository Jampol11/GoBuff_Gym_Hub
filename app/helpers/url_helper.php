<?php
/**
 * URL Helpers
 */

function base_url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function current_url(): string
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function is_active(string $path): string
{
    $current = $_SERVER['REQUEST_URI'] ?? '';
    return str_contains($current, $path) ? 'active' : '';
}

function pagination_links(array $pagination, string $baseUrl): string
{
    if ($pagination['total_pages'] <= 1) return '';

    $html  = '<nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0">';
    $page  = $pagination['current_page'];
    $total = $pagination['total_pages'];

    // Previous
    $prevDisabled = $page <= 1 ? 'disabled' : '';
    $prevHref     = $page > 1 ? $baseUrl . '?page=' . ($page - 1) : '#';
    $html .= "<li class=\"page-item {$prevDisabled}\"><a class=\"page-link\" href=\"{$prevHref}\">«</a></li>";

    // Pages
    $start = max(1, $page - 2);
    $end   = min($total, $page + 2);
    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $page ? 'active' : '';
        $html  .= "<li class=\"page-item {$active}\"><a class=\"page-link\" href=\"{$baseUrl}?page={$i}\">{$i}</a></li>";
    }

    // Next
    $nextDisabled = $page >= $total ? 'disabled' : '';
    $nextHref     = $page < $total ? $baseUrl . '?page=' . ($page + 1) : '#';
    $html .= "<li class=\"page-item {$nextDisabled}\"><a class=\"page-link\" href=\"{$nextHref}\">»</a></li>";

    $html .= '</ul></nav>';
    return $html;
}
