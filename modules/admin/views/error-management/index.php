<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 * @var $searchModel \app\modules\admin\models\BaseSearch
 * @var $fullData array
 */

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html; 
use yii\helpers\Url;

$this->title = Yii::t('app', 'Error Messages Management');

$libNames = array_unique(ArrayHelper::map($fullData, 'lib_name', 'lib_name'));
$functionNames = array_unique(ArrayHelper::map($fullData, 'func_name', 'func_name'));
$languages = array_unique(ArrayHelper::map($fullData, 'language', 'language'));

asort($libNames, SORT_NATURAL | SORT_FLAG_CASE);
asort($functionNames, SORT_NATURAL | SORT_FLAG_CASE);
asort($languages, SORT_NATURAL | SORT_FLAG_CASE);
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'lib_name',
            'filter' => $libNames
        ],
        [
            'attribute' => 'func_name',
            'filter' => $functionNames
        ],
        [
            'attribute' => 'language',
            'filter' => $languages
        ],
        'err_code',
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
<div class="button-block">
    <?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary']) ?>
</div>