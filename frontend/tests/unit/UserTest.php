<?php 
namespace frontend\tests;

use Yii;
use frontend\tests\fixtures\UserFixture;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */ 
    protected $tester;

    /**
     * метод описывающий какие fixture есть в этом классе
     */
    public function _fixtures()
    {
        return ['users' => UserFixture::className()];
    }

    public function _before()
    {
        /**
         * Подмена компонента для того чтобы приложение работало с базой данных 1 
         */
        Yii::$app->setComponents([
            'redis' => [
                'class' => 'yii\redis\Connection',
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 1,
            ],
        ]);
    }

    
    public function testGetNicknameOnNicknameEmpty()
    {
        /**
         * Получаем из fixture по ключу users пользователя user1
         * В итоге в переменной $user будет лежать объект User
         */
        $user = $this->tester->grabFixture('users', 'user1');
        /**
         * Ожидаем что метод getNickname() вернет единицу
         */
        expect($user->getNickname())->equals(1);        
    }
    
    public function testGetNicknameOnNicknameNotEmpty()
    {
        $user = $this->tester->grabFixture('users', 'user2');
        expect($user->getNickname())->equals('catelyn');  
    }
    
    public function testGetPostCount()
    {
        $user = $this->tester->grabFixture('users', 'user1');
        expect($user->getPostCount())->equals(3);  
    }
    
    public function testFollowUser()
    {
        $user1 = $this->tester->grabFixture('users', 'user1');
        $user3 = $this->tester->grabFixture('users', 'user3');
        
        /**
         * Подписываем пользователя 3 на пользователя 1
         */
        $user3->followUser($user1);
        
        /**
         * Проверяем подписан ли пользователь 3 на пользователя 1
         */
        $this->tester->seeRedisKeyContains('user:1:followers', 3);
        $this->tester->seeRedisKeyContains('user:3:subscriptions', 1);
        /**
         * Удаляем данные из редис после заполнения  
         *  */        
        $this->tester->sendCommandToRedis('del', 'user:1:followers');
        $this->tester->sendCommandToRedis('del', 'user:3:subscriptions');
    }
}