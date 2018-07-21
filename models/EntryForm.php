<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class EntryForm extends Model
{
    public $name;
    public $email;
   // public $rememberMe = true;

    //private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            ['email', 'email'],
        ];
    }
}
