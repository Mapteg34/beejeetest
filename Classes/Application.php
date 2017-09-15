<?php

namespace Mapt\Beejeetest;

use Mapt\Beejeetest\Controllers\ErrorPage;
use Mapt\Beejeetest\Controllers\Index;
use Mapt\Beejeetest\Controllers\HttpExceptionPage;
use Mapt\Beejeetest\Exceptions\HttpException;
use Mapt\Beejeetest\Exceptions\NotFound;
use Mapt\Beejeetest\Exceptions\ViewNotFound;
use Mapt\Beejeetest\Interfaces\Instanceable;

final class Application
    implements Instanceable
{
    /**
     * @var self
     */
    private static $instance = null;

    /**
     * @var string
     */
    private $appRoot;

    /**
     * @var Config
     */
    private $config = null;

    /**
     * @inheritdoc
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function appRoot()
    {
        return $this->appRoot;
    }

    private function __construct()
    {
        session_start();
        $path = realpath(__DIR__."/../");
        if (!$path) {
            die("Fatal: fail to determine project root");
        }
        $this->appRoot = $path;
    }

    /**
     * @return Config
     */
    public function config()
    {
        if ($this->config === null) {
            $this->config = new Config($this->appRoot."/conf/main.php");
        }

        return $this->config;
    }

    /**
     * @return string
     */
    public function fetch()
    {
        $content = "";
        try {
            $controller = null;
            $route      = "";

            $path = $_SERVER["REQUEST_URI"];
            if ($_SERVER["QUERY_STRING"]) {
                $path = substr($path, 0, 0 - strlen($_SERVER["QUERY_STRING"]) - 1);
            }

            if (in_array($path, ["/", "/index.php", "/index.html"])) {
                $controller = new Index();
            } else {
                $paths          = explode("/", $path);
                $controllerName = ucfirst(strtolower($paths[1]));
                $controllerPath = $this->appRoot."/Classes/Controllers/".$controllerName.".php";
                if ($controllerName && file_exists($controllerPath)) {
                    $className  = "Mapt\\Beejeetest\\Controllers\\".$controllerName;
                    $controller = new $className();
                } else {
                    throw new NotFound();
                }

                unset($paths[0]);
                unset($paths[1]);
                $route = implode("/", $paths);
            }
            $content = $controller->fetch($route);
        } catch (HttpException $e) {
            $controller = new HttpExceptionPage($e->getHttpCode(), $e->getMessage());
            $content    = $controller->fetch();
        } catch (\Exception $e) {
            $controller = new ErrorPage($e->getMessage());
            $content    = $controller->fetch();
        }

        return $content;
    }

    /**
     * @param string $fileName
     * @param array $params
     *
     * @return string
     * @throws ViewNotFound
     */
    public function includeView(string $fileName, array $params = [])
    {
        $filePath = realpath($this->appRoot."/views/".$fileName.".php");
        if (
            $filePath &&
            file_exists($filePath) &&
            substr($filePath, 0, strlen($this->appRoot)) == $this->appRoot &&
            substr($filePath, 0, strlen($this->appRoot."/www/")) != $this->appRoot."/www/"
        ) {
            extract($params);
            ob_start();
            include $filePath;
            $content = ob_get_contents();
            ob_end_clean();

            return $content;
        } else {
            throw new ViewNotFound($fileName);
        }
    }

    /**
     * @return Appuser
     */
    public function user()
    {
        return Appuser::instance();
    }

    /**
     * @return Database
     */
    public function db()
    {
        return Database::instance();
    }

    /**
     * @param string $url
     */
    public function redirect(string $url)
    {
        header('Location: '.$url);
        die();
    }
}