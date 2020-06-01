<?php


namespace App\Core;


use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class View
{
    public function render($template, $data)
    {
        ob_start();
        require_once $template;
        $templateBody = ob_get_clean();

        ob_start();
        require_once PROJECT_ROOT . '/templates/base.html.php';
        $template = ob_get_clean();

        $cache = new FilesystemAdapter('', 3600, PROJECT_ROOT . '/cache');
        $cacheItem = $cache->getItem(Kernel::getInstance()->getCacheKey());
        $cacheItem->set($template);
        $cache->save($cacheItem);

        echo $template;
    }
    public function sendJson(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function sendError(string $description)
    {
        header('Content-Type: application/json');

        $error = ['result' => 'error'];

        if (Kernel::getInstance()->getConfig()['show_json_error_description']) {
            $error['description'] = $description;
        }

        echo json_encode($error);
    }
}