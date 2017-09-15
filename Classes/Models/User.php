<?php

namespace Mapt\Beejeetest\Models;

use Mapt\Beejeetest\Model;

/**
 * Class User
 * @property string|null login
 * @property string|null email
 * @property bool|null is_admin
 * @property integer|null id
 * @property string|null hash
 * @package Mapt\Beejeetest\Models
 */
class User extends Model
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return "users";
    }
}