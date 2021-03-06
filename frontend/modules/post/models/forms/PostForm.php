<?php

namespace frontend\modules\post\models\forms;

use Yii;
use yii\base\Model;
use frontend\models\Post;
use frontend\models\User;
use Intervention\Image\ImageManager;
use frontend\models\events\PostCreatedEvent;


class PostForm extends Model
{

    const MAX_DESCRIPTION_LENGHT = 1000;
    /**
     * Событие пост создан
     */
    const EVENT_POST_CREATED = 'post_created';

    public $picture;
    public $description;

    private $user;
    
   

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['picture'], 'file',
                'skipOnEmpty' => false,
                'extensions' => ['jpg', 'png'],
                'checkExtensionByMimeType' => true,
                'maxSize' => $this->getMaxFileSize()],
                [['description'], 'string', 'max' => self::MAX_DESCRIPTION_LENGHT],
        ];
    }

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->on(self::EVENT_AFTER_VALIDATE, [$this, 'resizePicture']);
        /**
         * Если пост создан вызываем метод addToFeeds() компонента feedService
         */
        $this->on(self::EVENT_POST_CREATED, [Yii::$app->feedService, 'addToFeeds']);
        
    }

    /**
     * Изменяем изобр-е и сохраняем в тоже место   
     */
    /**
     * Resize image if needed
     */
    public function resizePicture()
    {
        $width = Yii::$app->params['postPicture']['maxWidth'];
        $height = Yii::$app->params['postPicture']['maxHeight'];

        $manager = new ImageManager(array('driver' => 'imagick'));

        $image = $manager->make($this->picture->tempName);     //    /tmp/11ro51

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();//пропорции изобр-я 
            $constraint->upsize();
        })->save();        //    /tmp/11ro51
    }

  
    /**
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            
            $post = new Post();
            $post->description = $this->description;
            $post->created_at = time();
            $post->filename = Yii::$app->storage->saveUploadedFile($this->picture);
            $post->user_id = $this->user->getId();
            if ($post->save(false)){
                $event = new PostCreatedEvent();
                $event->user = $this->user;
                $event->post = $post;
                $this->trigger(self::EVENT_POST_CREATED, $event);
                return true;
            }
            return false;
            
           
        }
        return false;
    }

    /**
     * Maximum size of the uploaded file
     * @return integer
     */
    private function getMaxFileSize()
    {
        return Yii::$app->params['maxFileSize'];
    }

}
