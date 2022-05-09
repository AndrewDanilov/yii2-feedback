<?php
namespace andrewdanilov\feedback;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadFile extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;
    public $maxFiles = 0;
    public $extensions = '';
    public $uploadDir = '@webroot/upload';

    public function rules()
    {
        return [
            [['files'], 'file', 'extensions' => $this->extensions, 'maxFiles' => $this->maxFiles],
        ];
    }

    public function upload()
    {
        $saved_files = [];
        $dir = Yii::getAlias($this->uploadDir);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        foreach ($this->files as $file) {
            $file_name = $dir . DIRECTORY_SEPARATOR . date('YmdHis') . '_' . $file->baseName . '.' . $file->extension;
            $file->saveAs($file_name);
            $saved_files[] = $file_name;
        }
        return $saved_files;
    }
}