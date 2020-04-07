<?php

namespace frontend\modules\user\controllers;

use Yii;
use yii\web\Controller;
use frontend\models\User;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\web\Response;
use frontend\modules\user\models\forms\PictureForm;


class ProfileController extends Controller{

    public function actionView($nickname){

        /* @var $currentUser User */
        /*Из компонента user берем объект User
        Пользователь который нажал на кнопку */ 
        $currentUser = Yii::$app->user->identity;
        /**
         * Задает правила для аватарок
         */
        $modelPicture = new PictureForm();


        return $this->render('view',[
            //Пользователь к которому мы хотим перейти на страницу 
            'user' => $this->findUser($nickname),
            'currentUser' => $currentUser,
            'modelPicture' => $modelPicture,
        ]);

    }

    private function findUser($nickname){
        if($user = User::find()->where(['nickname' => $nickname])->orWhere(['id'=>$nickname])->one()){
            return $user;
        }
        throw new NotFoundHttpException(); 

    }

     /**
     * Подписка
     */
    public function actionSubscribe($id){
        if(Yii::$app->user->isGuest){
            return $this->redirect(['/user/default/login']);

        }
         /* @var $currentUser User
         Из компонента user берем объект User
         Пользователь который нажал на кнопку 
         */
        $currentUser = Yii::$app->user->identity;
        //Берем пользователя на которого нужно подписаться 
        $user = $this->findUserById($id);
        /**
         * Пользователя который нажал на кнопку подписываем на пользователя user
         */
        $currentUser->followUser($user);

        return $this->redirect(['/user/profile/view','nickname' => $user->getNickname()]);

    }

    public function actionUnsubscribe($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/default/login']);
        }

        /* @var $currentUser User */
        $currentUser = Yii::$app->user->identity;
        $user = $this->findUser($id);

        $currentUser->unfollowUser($user);

        return $this->redirect(['/user/profile/view', 'nickname' => $user->getNickname()]);
    }

    private function findUserById($id){

        if($user = User::findOne($id)){
            return $user;

        }

        throw new NotFoundHttpException();
    }

    /**
     * Handle profile image upload via ajax request
     */
    public function actionUploadPicture()
    {
        //Чтобы изображение обновлялось сразу после загрузки
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new PictureForm();
        $model->picture = UploadedFile::getInstance($model, 'picture');

        if ($model->validate()) { 

            //Загружаем текущего пользователя
            $user = Yii::$app->user->identity;
            //Загружаем изображение
            $user->picture = Yii::$app->storage->saveUploadedFile($model->picture); 
            //Сохраняем пользователя без валидации 
            if ($user->save(false, ['picture'])) {
                return [
                    'success' => true, 
                    'pictureUri' => Yii::$app->storage->getFile($user->picture),
                ];
            }
        }
        return ['success' => false, 'errors' => $model->getErrors()];
    }   
    
        // public function actionGenerate(){

        //     $faker = \Faker\Factory::create();

        //     for($i = 0;$i<100;$i++){
        //         $user = new User([
        //             'username' => $faker->name,
        //             'email' => $faker->email,
        //             'about' => $faker->text(200),
        //             'nickname' => $faker->regexify('[A-Za-z0-9_]{5,15}'),
        //             'auth_key' => Yii::$app->security->generateRandomString(),
        //             'password_hash' => Yii::$app->security->generateRandomString(),
        //             'created_at' => $time = time(),
        //             'updated_at' =>$time,
        //         ]);
        //         $user->save(false);
        //     }
        // }

}