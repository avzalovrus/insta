<?php

namespace frontend\modules\post\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\models\Post;
use frontend\modules\post\models\forms\PostForm;

/**
 * Default controller for the `post` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionCreate()
    {   
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        /**
         * Получаем данные пользователя который выполняет текущее действие
         */
        $model = new PostForm(Yii::$app->user->identity);
        
        
        /**
         * Если форма была отправлена
         */
        
        if (Yii::$app->request->isPost) {
            
            /**
             * В модели свойство picture получает данные из формы поля picture 
             */
            $model->picture = UploadedFile::getInstance($model, 'picture');
            if ($model->save()) {
                
                Yii::$app->session->setFlash('success', 'Post created!');
                return $this->goHome();
            }
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Renders the create view for the module
     * @return string
     */
    public function actionView($id)
    {        
        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;    
        
        return $this->render('view', [
            'post' => $this->findPost($id),
            'currentUser' => $currentUser,
        ]);
    }

    public function actionLike()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        /**
         * Получаем id posta 
         */
        $id = Yii::$app->request->post('id');
        $post = $this->findPost($id);
        
        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;        
        
        $post->like($currentUser);

        return [
            'success' => true,
            'likesCount' => $post->countLikes(),
        ];     
    }

    public function actionUnlike()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        $post = $this->findPost($id);

        $post->unLike($currentUser);

        return [
            'success' => true,
            'likesCount' => $post->countLikes(),
        ];
    }

    public function actionComplain()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }
        /**
         * Формат ответа json
         */
        Yii::$app->response->format = Response::FORMAT_JSON;

        /**
         * id поста на который пожаловались
         */
        $id = Yii::$app->request->post('id');

        /**
         * Текущий пользователь который пожаловался на пост
         */
        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        /**
         * Поиск поста на который пожаловались
         */
        $post = $this->findPost($id);

        if ($post->complain($currentUser)) {
            return [
                'success' => true,
                'text' => 'Post reported'
            ];
        }
        return [
            'success' => false,
            'text' => 'Error',
        ];
    }



    /**
     * @param integer $id
     * @return User
     * @throws NotFoundHttpException
     */
    private function findPost($id)
    {
        if ($user = Post::findOne($id)) {
            return $user;
        }
        throw new NotFoundHttpException();
    }
}
