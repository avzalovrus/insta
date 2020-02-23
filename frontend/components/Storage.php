<?php

namespace frontend\components;

use Yii;
use yii\base\Component;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

/**
 * File storage compoment
 *
 * @author admin
 */
class Storage extends Component implements StorageInterface
{

    private $fileName;

    /**
     * Save given UploadedFile instance to disk
     * @param UploadedFile $file
     * @return string|null
     */
    public function saveUploadedFile(UploadedFile $file)
    {   //Получаем путь
        $path = $this->preparePath($file); 
        //Сохраняем файл
        if ($path && $file->saveAs($path)) {
            return $this->fileName;
        }
    }

    /**
     * Prepare path to save uploaded file
     * @param UploadedFile $file
     * @return string|null
     */
    protected function preparePath(UploadedFile $file)
    {
        $this->fileName = $this->getFileName($file);  
        //     0c/a9/277f91e40054767f69afeb0426711ca0fddd.jpg
        
         /**
         * Полный путь к файлу который можно сохранить на диск
         */
        $path = $this->getStoragePath() . $this->fileName;  
        //     /var/www/project/frontend/web/uploads/0c/a9/277f91e40054767f69afeb0426711ca0fddd.jpg
        //Нормализуем путь
        $path = FileHelper::normalizePath($path);
        //Создаем папку
        if (FileHelper::createDirectory(dirname($path))) {
            return $path;
        }
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function getFilename(UploadedFile $file)
    {
         //Место где находится физический файл после загрузки
        // $file->tempname   -   /tmp/qio93kf
        
          //Подсчет хэш суммы при помощи функции sha1_file()
        $hash = sha1_file($file->tempName); // 0ca9277f91e40054767f69afeb0426711ca0fddd
        
         /**
         * Добавляем слэши и расширение jpg
         */
        $name = substr_replace($hash, '/', 2, 0);
        $name = substr_replace($name, '/', 5, 0);  // 0c/a9/277f91e40054767f69afeb0426711ca0fddd
        return $name . '.' . $file->extension;  // 0c/a9/277f91e40054767f69afeb0426711ca0fddd.jpg
    }

    /**
     * @return string
     */
    protected function getStoragePath()
    {
        return Yii::getAlias(Yii::$app->params['storagePath']);
    }

    /**
     * 
     * @param string $filename
     * @return string
     */
    public function getFile(string $filename)
    {
        return Yii::$app->params['storageUri'].$filename;
    }
}
