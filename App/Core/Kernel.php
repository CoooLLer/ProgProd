<?php


namespace App\Core;


use App\Core\Interfaces\DatabaseInterface;
use Exception;

class Kernel
{
    public DatabaseInterface $database;

    public array $config;

    public Router $router;

    public View $view;

    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) self::$instance = new static();

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    private function __construct()
    {

    }

    public function init(array $config, array $routes)
    {
        $this->config = $config;
        $this->router = new Router($routes);
        $this->database = $this->initDatabase();
        $this->view = new View();
    }

    private function initDatabase(): DatabaseInterface
    {
        //Заморочимся немного с универсальностью, но без полноценной абстракции от типа БД, репозитории сущностей будут подразумевать, что у нас mysqli через PDO prepared statements
        $databaseType = $this->config['database']['type'];

        $databaseModulePath = PROJECT_ROOT . $this->config['database']['modules_path'] . DIRECTORY_SEPARATOR . ucfirst($databaseType) . DIRECTORY_SEPARATOR . ucfirst($databaseType) . '.php';
        if (!file_exists($databaseModulePath)) {
            throw new Exception(sprintf('Database module %s not found', $databaseModulePath));
        }

        $databaseClass = str_replace('/', '\\', $this->config['database']['modules_path'] . DIRECTORY_SEPARATOR . ucfirst($databaseType) . DIRECTORY_SEPARATOR . ucfirst($databaseType));
        $database = new $databaseClass($this->config['database']);

        if (!in_array(DatabaseInterface::class, class_implements($database))) {
            throw new Exception(sprintf('Database module %s not implemented %s interface', $database, DatabaseInterface::class));
        }

        return $database;
    }

    public function run()
    {
        $this->router->dispatch();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return DatabaseInterface
     */
    public function getDatabase(): DatabaseInterface
    {
        return $this->database;
    }

    /**
     * @return View
     */
    public function getView(): View
    {
        return $this->view;
    }

    public function getCacheKey()
    {
        return md5($this->router->action . implode('&', $_GET) . implode('&', $_POST));
    }
}