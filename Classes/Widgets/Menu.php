<?php

namespace Mapt\Beejeetest\Widgets;

use Mapt\Beejeetest\Widget;

class Menu extends Widget
{

    /**
     * @var array
     */
    private $items = [];

    /**
     * Menu constructor.
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function fetch()
    {
        $rootItems = ["/", "/index.php", "/index.html"];
        foreach ($this->items as &$item) {
            if (in_array($item["href"], $rootItems)) {
                if (in_array($_SERVER["REQUEST_URI"], $rootItems)) {
                    $item["active"] = true;
                    break;
                }
            } elseif (substr($_SERVER["REQUEST_URI"], 0, strlen($item["href"])) == $item["href"]) {
                $item["active"] = true;
                break;
            }
        }
        unset($item);

        return app()->includeView("menu", ["items" => $this->items]);
    }

}