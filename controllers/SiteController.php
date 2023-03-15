<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\controllers;

use app\components\AuthHelper;
use app\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
        ];
    }

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

    public function actionIndex()
    {
        //return $this->render('index');
        return Yii::$app->user->isGuest ? $this->redirect(['login']) : $this->redirect(['/site/main']);
    }

    public function actionAliasAjax()
    {
        $this->view->params['showBear'] = true;
        return $this->render('main');
    }

    public function actionMain()
    {
        $this->view->params['showBear'] = true;
        return $this->render('main');
    }

    public function actionChecklogin()
    {
        $result = [];
        $session_handle = $_SESSION['screenData']['sessionData']['sessionhandle'];
        if (Yii::$app->user->isGuest) {
            $result = "false";
        } else {
            $result = $session_handle;
        }
        echo $result;
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && AuthHelper::init()) {
            (Yii::$app->request->post()['LoginForm']['username'] == 'tonib') ? AuthHelper::completeType(AuthHelper::AUTH_TYPE_LDAP) : AuthHelper::completeType(AuthHelper::AUTH_TYPE_LOGIN);

            if (AuthHelper::getStatus() == AuthHelper::STATUS_PROGRESS) {
                $nextType = AuthHelper::getCurrentType();
                return $this->redirect(['auth/' . AuthController::$actionMask[$nextType]]);
            } else {
                $model->login();
            }

            return $this->goBack();
        } else {
            $session = Yii::$app->session;
            unset($session['screenData']);
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
