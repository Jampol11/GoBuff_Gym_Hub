<?php
/**
 * Base Controller
 */
abstract class Controller
{
    protected Session $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Render a view with layout
     */
    protected function view(string $view, array $data = [], string $layout = 'default'): void
    {
        // Extract data to local variables
        extract($data);

        // Capture view content
        ob_start();
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            die("View not found: {$viewFile}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Render layout
        if ($layout === 'auth') {
            require VIEWS_PATH . '/layouts/auth_layout.php';
        } else {
            require VIEWS_PATH . '/layouts/main_layout.php';
        }
    }

    /**
     * Redirect to a URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . base_url($url));
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Set flash message
     */
    protected function flash(string $type, string $message): void
    {
        $this->session->setFlash($type, $message);
    }

    /**
     * Get current authenticated user
     */
    protected function currentUser(): array|null
    {
        return $this->session->get('user');
    }

    /**
     * Check if user has role
     */
    protected function hasRole(string|array $roles): bool
    {
        $user = $this->currentUser();
        if (!$user) return false;
        $roles = (array) $roles;
        return in_array($user['role'], $roles);
    }

    /**
     * Abort with HTTP error
     */
    protected function abort(int $code = 403): void
    {
        http_response_code($code);
        $messages = [403 => 'Forbidden', 404 => 'Not Found', 500 => 'Server Error'];
        $msg = $messages[$code] ?? 'Error';
        die("<h1>{$code} - {$msg}</h1>");
    }

    /**
     * Paginate helper
     */
    protected function paginate(int $total, int $page, int $perPage = RECORDS_PER_PAGE): array
    {
        $totalPages = (int) ceil($total / $perPage);
        $page       = max(1, min($page, $totalPages));
        $offset     = ($page - 1) * $perPage;
        return [
            'total'       => $total,
            'per_page'    => $perPage,
            'current_page'=> $page,
            'total_pages' => $totalPages,
            'offset'      => $offset,
        ];
    }
}
