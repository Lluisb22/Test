<?php

namespace app\models;


use app\models\Users;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
	
	
    //private $users = Users::find()->all();

	
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
		$user = Users::findOne($id);
		if ($user == null)
		{
			return null;
		}
		else
		{
			$id = $user->id;
			$username = $user->username;
			$password = $user->password;
			$authKey = $user->authKey;
			$accessToken = $user->accessToken;
			return $user;
	
		}
		//return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
		$user = Users::FindOne(['token' => $token]);
       $id = $user->id;
			$username = $user->username;
			$password = $user->password;
			$authKey = $user->authKey;
			$accessToken = $user->accessToken;
	   return $user == null ? null : new static($user);
	   /* foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;*/
    }

    /**
     * Finds user by username
     *
     * @param string $username        
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = Users::FindOne(['username' => $username]);
		
		if ($user == null)
		{
			return null;
		}
		else
		{
			$id = $user->id;
			$username = $user->username;
			$password = $user->password;
			$authKey = $user->authKey;
			$accessToken = $user->accessToken;
			return new User();
	
		}
       /*foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;*/
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}
