<?php

namespace frontend\tests\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    /**
     * Модель с которой связана fixture
     */
    public $modelClass = 'frontend\models\User';
    /**
     * Таблица user зависит от таблицы post
     */
    public $depends = [
        'frontend\tests\fixtures\PostFixture'
    ];

}