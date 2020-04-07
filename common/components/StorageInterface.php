<?php
namespace common\components;

use yii\web\UploadedFile;

interface StorageInterface{
    
    public function saveUploadedFile(UploadedFile $file);
    
    /**
     * Получение полного пути файла по его имени
     */
    public function getFile(string $fileName);

}