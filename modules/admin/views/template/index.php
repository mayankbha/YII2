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
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Admin templates');

$libNames = array_unique(ArrayHelper::map($fullData, 'lib_name', 'lib_name'));
$dataSource = array_unique(ArrayHelper::map($fullData, 'data_source', 'data_source'));
$aliasTables = array_unique(ArrayHelper::map($fullData, 'alias_table', 'alias_table'));

asort($libNames, SORT_NATURAL | SORT_FLAG_CASE);
asort($dataSource, SORT_NATURAL | SORT_FLAG_CASE);
asort($aliasTables, SORT_NATURAL | SORT_FLAG_CASE);
?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'lib_name',
            'filter' => $libNames
        ],
        [
            'attribute' => 'data_source',
            'filter' => $dataSource
        ],
        [
            'attribute' => 'alias_table',
            'filter' => $aliasTables
        ],
        'alias_field',
        'field_type',
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