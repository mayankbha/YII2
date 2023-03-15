<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 * @var $searchModel \app\modules\admin\models\BaseSearch
 * @var $fullData array
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\GetListList;

$this->title = Yii::t('app', 'Admin extension functions');
$extensionsList = GetListList::getArrayForSelectByNames([GetListList::BASE_NAME_EXTENSION], true, false);

$libNames = array_unique(ArrayHelper::map($fullData, 'datasource_lib', 'datasource_lib'));
$functions = array_unique(ArrayHelper::map($fullData, 'datasource_func', 'datasource_func'));

asort($libNames, SORT_NATURAL | SORT_FLAG_CASE);
asort($functions, SORT_NATURAL | SORT_FLAG_CASE);
?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'datasource_lib',
            'filter' => $libNames
        ],
        [
            'attribute' => 'datasource_func',
            'filter' => $functions
        ],
        'extension_func',
        'extension_freemem_func',
        [
            'attribute' => 'extension_type',
            'filter' => $extensionsList[GetListList::BASE_NAME_EXTENSION]
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '70px'],
            'template' => '{update} {delete}'
        ]
    ],
    'tableOptions' => [
        'class' => 'table table-hover'
    ],
]);
?>
<div class="button-block"><?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary pull-right']) ?></div>