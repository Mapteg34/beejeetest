<?php

namespace Mapt\Beejeetest\Models;

use Mapt\Beejeetest\Model;

/**
 * Class Task
 * @property integer|null id
 * @property integer|null created
 * @property string|null text
 * @property integer|null user_id
 * @property bool|null completed
 * @property string|null image_path
 * @package Mapt\Beejeetest\Models
 */
class Task extends Model
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return "tasks";
    }

    /**
     * @return array
     */
    public static function references()
    {
        return [
            "users AS u" => [
                "type" => "LEFT JOIN",
                "on"   => "u.id=tasks.user_id"
            ]
        ];
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function convert(string $text)
    {
        return str_replace("\n", "<br/>\n", htmlspecialchars($text));
    }
}