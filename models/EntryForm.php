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

    private $_user = false;


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
	
	    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getNewUser()
    {
        if ($this->_user === false) {
            $this->_user =  new Users();
			$this->_user->username = $name;
			$this->_user->email = $email;
			$this->_user->authKey = 'test1111Key';
			$this->_user->accessToken = 'token-Prova';
			$this->_user->save();
        }

        return $this->_user;
    }
}
