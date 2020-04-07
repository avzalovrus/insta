<?php
namespace frontend\controllers;

use yii\web\Controller;
use yii\web\Cookie;
use frontend\models\User;
use Yii;


/**
 * Site controller
 */
class SiteController extends Controller
{
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

        

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;

        $limit = Yii::$app->params['feedPostLimit'];
        /**
         *Берем данные новостной ленты  
         */ 
        $feedItems = $currentUser->getFeed($limit);

        return $this->render('index', [
            'feedItems' => $feedItems,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * About page
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Change language
     * @return mixed
     */
    public function actionLanguage()
    {
        // Hometask: check if language is supported        
        $language = Yii::$app->request->post('language');
        Yii::$app->language = $language;

        $languageCookie = new Cookie([
            'name' => 'language',
            'value' => $language,
            'expire' => time() + 60 * 60 * 24 * 30, // 30 days
        ]);
        Yii::$app->response->cookies->add($languageCookie);
        return $this->redirect(Yii::$app->request->referrer);
    }


    
}
