<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\controllers;

use app\models\UserAccount;
use yii\web\Controller;
use yii\filters\AccessControl;
use Yii;
use yii\base\UserException;
use yii\helpers\Url;

class ModuleController extends Controller
{
    /** @var UserAccount|null $user */
    protected $user;

    public function beforeAction($event)
    {
        if (Yii::$app->session->has('lang')) {
            Yii::$app->language = Yii::$app->session->get('lang');
        } else {
           // or you may want to set lang session, this is just a sample
            Yii::$app->language = 'en-US';
        }  

        if (Yii::$app->user->isGuest) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->redirect(Url::toRoute(['/login']));
            } else {
                $this->redirect(Url::toRoute(['/login']));
            }
            return false;
        }
        return parent::beforeAction($event);
    }

    public function afterAction($action, $result)
    {
        if (Yii::$app->user->isGuest) {
            $this->redirect(Url::toRoute(['/login']));
            return false;
        }
        return parent::afterAction($action, $result);
    }

    /**
     * @return UserAccount|null
     */
    protected function getUser()
    {
        if (!$this->user) {
            $this->user = Yii::$app->user->getIdentity();
        }

        return $this->user;
    }
}