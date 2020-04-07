<?php

namespace frontend\models\events;

use yii\base\Event;
use frontend\models\User;
use frontend\models\Post;

class PostCreatedEvent extends Event
{

    /**
     * Автор поста
     * @var User 
     */
    public $user;

    /**
     *Пост
     * @var Post
     */
    public $post;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

}
