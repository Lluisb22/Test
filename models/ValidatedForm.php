<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Users;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class ValidatedForm extends Model
{
    //public $user;
    public $password = false;
	public $repeat_password;
	public $token;
    //public $rememberMe = true;

    //private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return array(
            //password and repeat password
            array(['password, repeat_password'], 'required', 'on' => 'Validated'),
            //array(['password, repeat_password'], 'string', 'min'=>6, 'max'=>40),
            //array('repeat_password', 'compare', 'compareAttribute' =>'password'),
            
		);
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
	 
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false; 
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findIdentityByAccessToken($this->token);
        }

        return $this->_user;
    }
}
