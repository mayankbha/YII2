<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\data\ArrayDataProvider;

class BaseSearch extends DynamicModel
{
    /** @var $_formModel Model */
    private $_formModel;
    private $_data;

    public function __construct($modelClass)
    {
        /** @var $model BaseModel */
        $model = new $modelClass();
        $formClass = $model::$formClass;

        $this->_data = (($data = $model::getData()) && !empty($data->list)) ? $data->list : [];
        $this->_formModel = new $formClass();

        $this->addRule($this->_formModel->attributes(), 'string');

        parent::__construct($this->_formModel->attributes());
    }

    public function getData() {
        return $this->_data;
    }

    public function search($params)
    {
        $this->load($params);
        $formModel = $this->_formModel;
        $attributes = array_filter($this->getAttributes(null, ['_model', '_formModel', '_data']));
        $data = array_filter($this->getData(), function ($v) use ($attributes) {
            foreach($attributes as $attrKey => $attrValue) {
                $haystack = strtolower($v[$attrKey]);
                $needle = strtolower(trim($attrValue));

                if (strpos($haystack, $needle) === false) {
                    return false;
                }
            }
            return true;
        });

        $sort = is_array(current($data)) ? [
            'attributes' => array_keys(current($data)),
            'defaultOrder' => (!empty($formModel::$defaultSort)) ? $formModel::$defaultSort : false
        ] : false;

        $dataProvider = new ArrayDataProvider([
            'key' => 'pk',
            'allModels' => $data,
            'modelClass' => $this->_formModel,
            'sort' => $sort,
            'pagination' => ['pageSize' => 10]
        ]);

        return $dataProvider;
    }
}