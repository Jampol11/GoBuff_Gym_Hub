<?php
/**
 * App - Application bootstrap
 */
class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function run(): void
    {
        // Boot helpers
        require_once APP_PATH . '/helpers/url_helper.php';
        require_once APP_PATH . '/helpers/auth_helper.php';
        require_once APP_PATH . '/helpers/security_helper.php';

        // Start session
        $session = new Session();
        Auth::init($session);

        // Load routes
        $router = $this->router;
        require_once APP_ROOT . '/routes/web.php';

        // Dispatch
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->router->dispatch($uri, $method);
    }
}
