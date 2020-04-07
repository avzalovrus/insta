<?php
namespace frontend\tests\models;

use frontend\tests\fixtures\UserFixture;
use frontend\modules\user\models\SignupForm;

class SignupFormTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;
    
    public function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::className(),
            ]
        ]);
    }
    /**
     * Добавляем имя пользователя с пробелами и после теста они должны быть обрезаны
     */
    public function testTrimUsername()
    {
        /**
         * Заполняем форму
         */
        $model = new SignupForm([
            'username' => ' some_username ',
            'email' => 'some_email@example.com',
            'password' => '12345',
        ]);
        /**
         * обрезание пробелов
         */
        $model->signup();
        
        expect($model->username)
                ->equals('some_username');
    }
    
    public function testUsernameRequired()
    {
        $model = new SignupForm([
            'username' => '',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
        ]);

        $model->signup();
        /**
         * Должна выйти ошибка Username cannot be blank
         */
        expect($model->getFirstError('username'))
                ->equals('Username cannot be blank.');
    }
    
    public function testUsernameTooShort()
    {
        $model = new SignupForm([
            'username' => 's',
            'email' => 'some_email@example.com',
            'password' => 'some_password',
        ]);

        $model->signup();

        expect($model->getFirstError('username'))
                ->equals('Username should contain at least 2 characters.');
    }
    
    public function testPasswordRequired()
    {
        $model = new SignupForm([
            'username' => 'some_username',
            'email' => 'some_email@example.com',
            'password' => '',
        ]);

        $model->signup();

        expect($model->getFirstError('password'))
                ->equals('Password cannot be blank.');
    }    

    public function testEmailUnique()
    {
        $model = new SignupForm([
            'username' => 'some_username',
            'email' => '1@got.com',
            'password' => 'some_password',
        ]);

        $model->signup();
        
        sleep(10);

        expect($model->getFirstError('email'))
                ->equals('This email address has already been taken.');
    }
}