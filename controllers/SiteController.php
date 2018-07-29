<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\EntryForm;
use app\models\ValidatedForm;
use app\models\Users;



require_once '../vendor/autoload.php';
		

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login', 'logout', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
				],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
	
	/*
	* Say 'Hello world'
	*
	* @return string
	*/
	public function actionSay($message = 'Hola')
	{
		return $this->render('say', ['message' => $message]);
	}
	
	function encrypt_decrypt($action, $string) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = '22011985 - provando';
		$secret_iv = 'This is my secret iv 11111';
		// hash
		$key = hash('sha256', $secret_key);
		
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if( $action == 'decrypt' ) {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
	
	
	/*
	* New User access
	*
	* @return string
	*/
	public function actionEntry()
	{

		$model = new EntryForm;
		
		if ($model->load(Yii::$app->request->post()) && $model->validate())
		{
			
			$user=  new Users();
			$user->username = $model->name;
			$user->email = $model->email;
			$user->authKey = 'test1111Key';
			$user->accessToken = 'token-'.rand(0,999999).'-'.$model->name;
			$user->save();
			$string =  ['token' => $user->accessToken, 'date'=>Date('Y-m-d H:i:s'),];
			$token = $this->encrypt_decrypt('encrypt', http_build_query($string));
			
			$content = 'Click de following link to ends your registry: http://localhost:8080/index.php?r=site/validated&token='.$token; 
			
			$numSent =Yii::$app->mailer->compose('layouts/html', ['content' => $content])
				->setFrom('baycat@gmail.com')
				->setTo($model->email)
				->setSubject('Prova 1')
				->send();
				
			return $this->render('entry-confirm', ['model' => $model]);
		} else {
			// la ´apgina es mostrada inicialmente o hay ´ualgn error de ´ovalidacin
			return $this->render('entry', ['model' => $model]);
		}
	}
	
	
	/*
	* Validated new user
	*
	* @return string
	*/
	public function actionValidated($token = null)
	{
		$model = new ValidatedForm;
		if (isset($_POST['ValidatedForm'])){
			$attributes = $_POST['ValidatedForm'];
			if($attributes['password'] != $attributes['repeat_password'])
			{
				return $this->render('validated-confirm', ['message' => 'Incorrect Passwords']);
			}
			else{
				$decrypted = $this->encrypt_decrypt('decrypt', $token);
				parse_str( $decrypted,$data);
				$user = Users::findOne(['accessToken' => $data['token']]);
				$db_TS = \DateTime::createFromFormat('Y-m-d H:i:s', $user->TimeStamp);
				$tk_TS = \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']);
				if ($db_TS->diff($tk_TS,true) > new \DateInterval('PT2S')){
					return $this->render('validated-confirm', ['message' => 'Time Stamp do not match']);
				}
				if($user == null){
					return $this->render('validated-confirm', ['message' => 'User not found in DB']);
				}
				else{
					Users::updateAll(array("password" => $attributes['password']), ' id = '.$user->id);
					
					$model = new LoginForm();
					$model->username = $user->username;
					$model->password = $attributes['password'];
					$model->login();
					
					return $this->render('validated-confirm', ['message' => 'Wellcome!!!']);
				}
			}
		}
		else {
			$decrypted = $this->encrypt_decrypt('decrypt', $token);
			parse_str( $decrypted,$data);
			$user = Users::findOne(['accessToken' => $data['token']]);
			$db_TS = \DateTime::createFromFormat('Y-m-d H:i:s', $user->TimeStamp);
			$tk_TS = \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']);
			$now = \DateTime::createFromFormat('Y-m-d H:i:s',Date('Y-m-d H:i:s'));
			if ($db_TS->diff($tk_TS,true) > new \DateInterval('PT2S')){
				return $this->render('validated-confirm', ['message' => 'This is not a correct Token']);
			}
			else if($now->diff($tk_TS,true) > new \DateInterval('PT2H')){
				return $this->render('validated-confirm', ['message' => 'The token expires']);
			}
			else{
				return $this->render('validated', ['model' => $model]);
			}
		}
	}
}
