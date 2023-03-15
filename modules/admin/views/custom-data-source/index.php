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
use app\models\CustomLibs;
use app\models\BaseModel;

$this->title = Yii::t('app', 'Admin templates');

$tables = array_unique(ArrayHelper::map($fullData, 'func_table', 'func_table'));
asort($tables, SORT_NATURAL | SORT_FLAG_CASE);
?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        'func_name',
        [
            'attribute' => 'func_type',
            'filter' => array_combine(BaseModel::$functionTypes, BaseModel::$functionTypes)
        ],
        [
            'attribute' => 'func_table',
            'filter' => $tables
        ],
        [
            'attribute' => 'func_direction_type',
            'filter' => array_combine(CustomLibs::$directionTypes, CustomLibs::$directionTypes)
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