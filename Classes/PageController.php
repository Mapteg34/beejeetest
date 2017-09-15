<?php

namespace Mapt\Beejeetest;


use Mapt\Beejeetest\Exceptions\NotFound;

abstract class PageController extends Controller
{
    /**
     * @var string[]
     */
    private $arJs = [];

    /**
     * @var string[]
     */
    private $arCss = [];

    /**
     * @var string
     */
    private $title = "Beejee Test";

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param string $jsUrl
     */
    public function addJs(string $jsUrl)
    {
        $this->arJs[] = $jsUrl;
    }

    /**
     * @param string $cssUrl
     */
    public function addCss(string $cssUrl)
    {
        $this->arCss[] = $cssUrl;
    }

    /**
     * @param string $route
     *
     * @return string
     */
    public function getRouteAction(string $route)
    {
        if ($route == "") {
            return "index";
        }
        $route  = explode("/", $route);
        $action = $route[0];
        unset($route[0]);
        $route = implode("/", $route);
        if ($route) {
            return "404";
        }

        return $action;
    }

    /**
     * @throws NotFound
     */
    public function action404()
    {
        throw new NotFound();
    }

    /**
     * @param string $route
     *
     * @return string
     * @throws NotFound
     */
    public function fetch(string $route = "")
    {
        $action = "action".ucfirst(strtolower($this->getRouteAction($route)));

        if (!method_exists($this, $action)) {
            throw new NotFound();
        }

        $this->addJs("//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js");

        $content = $this->{$action}();

        $result = app()->includeView("main", [
            "title"      => "test",
            "content"    => $content,
            "controller" => $this
        ]);

        return $result;
    }

    /**
     * @return string
     */
    public function showHead()
    {
        $content = "";
        foreach ($this->arCss as $cssUrl) {
            $content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".htmlspecialchars($cssUrl, ENT_QUOTES)."\" />\n";
        }
        foreach ($this->arJs as $jsUrl) {
            $content .= "<script type=\"text/javascript\" src=\"".htmlspecialchars($jsUrl, ENT_QUOTES)."\"></script>\n";
        }

        return $content;
    }

    /**
     * @return string
     */
    public function showTitle()
    {
        return htmlspecialchars($this->title);
    }
}