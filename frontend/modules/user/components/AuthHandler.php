<?php
namespace frontend\modules\user\components;

use Yii;
use frontend\modules\user\models\Auth;
use frontend\models\User;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{

    /**
     * @var ClientInterface
     */
    private $client;

    /*
    Содержится объект который знает как пользоваться провайдером facebook
    */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {   
        if (!Yii::$app->user->isGuest) {
            return;
        }

        /*
        Происходит запрос к facebook для получения данных пользователя
        */
        $attributes = $this->client->getUserAttributes();
        /*
        Чтобы определить сохранять данные пользователя который прислал facebook или нет:
        Выполняем поиск по таблице auth используя ид 
        С помощью связи с таблицей User получаем объект польхователя связанный с данной записью
        */
        $auth = $this->findAuth($attributes);
        if ($auth) {
            /* @var User $user */
            $user = $auth->user;
            return Yii::$app->user->login($user);
        }
        /**
         * Иначе создаем аккаунт пользователя и логинимся
         */
        if ($user = $this->createAccount($attributes)) {
            return Yii::$app->user->login($user);
        }
    }
    /**
     * @param array $attributes
     * @return Auth
     */
    private function findAuth($attributes)
    {
        $id = ArrayHelper::getValue($attributes, 'id');//По ключу id происходит поиск  
        $params = [
            'source_id' => $id,//идентификатор
            'source' => $this->client->getId(),//название соц сети 
        ];
        return Auth::find()->where($params)->one();
    }

    /**
     * Создание аккаунта состоит из двух частей:
     * Сохранение данных в таблицу User и в таблицу Auth
     * 
     */
    /**
     * 
     * @param type $attributes
     * @return User|null
     */
    private function createAccount($attributes)
    {
        $email = ArrayHelper::getValue($attributes, 'email');
        $id = ArrayHelper::getValue($attributes, 'id');
        $name = ArrayHelper::getValue($attributes, 'name');

        /**
         * Если email пуст и занят то регистрации не произойдет
         */
        if ($email !== null && User::find()->where(['email' => $email])->exists()) {
            return;
        }

        $user = $this->createUser($email, $name);
        /**
         * Операции сохранения в таблицу User и Auth должны быть атомарными, поэтому используется транзакция
         */
        $transaction = User::getDb()->beginTransaction();
        if ($user->save()) {
            $auth = $this->createAuth($user->id, $id);
            if ($auth->save()) {
                $transaction->commit();
                return $user;
            }
        }
        //Если транзакция не прошла, то откат изменений
        $transaction->rollBack();
    }

    /**
     * Создается объект класса User Activerecord
     * И назначается имя email, пришедшие от провайдера
     */
    private function createUser($email, $name)
    {
        return new User([
            'username' => $name,
            'email' => $email,
            'auth_key' => Yii::$app->security->generateRandomString(),
            //База не хранит пароль в обычном виде
            //Пароль должен быть обязательно создан
            'password_hash' => Yii::$app->security->generatePasswordHash(Yii::$app->security->generateRandomString()),
            'created_at' => $time = time(),
            'updated_at' => $time,
        ]);
    }

    private function createAuth($userId, $sourceId)
    {
        return new Auth([
            'user_id' => $userId,
            'source' => $this->client->getId(),
            'source_id' => (string) $sourceId,
        ]);
    }

}