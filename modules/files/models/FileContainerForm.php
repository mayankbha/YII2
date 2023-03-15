<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\files\models;

use Yii;
use yii\base\Model;

class FileContainerForm extends Model
{
    public $id;
    public $chunk_size;
    public $chunk_uploaded;
    public $container_file_name;
    public $container_file_path;
    public $document_category;
    public $document_family;
    public $document_key;
    public $original_file_attributes;
    public $original_file_hash;
    public $original_file_name;
    public $original_file_size;
    public $upload_status;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['chunk_size', 'chunk_uploaded', 'container_file_name', 'container_file_path'], 'safe'],
            [['document_category', 'document_family', 'document_key'], 'safe'],
            [['original_file_attributes', 'original_file_hash', 'original_file_name', 'original_file_size'], 'safe'],
            [['upload_status'], 'safe'],
        ];
    }
}