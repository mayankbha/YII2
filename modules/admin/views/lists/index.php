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

$this->title = Yii::t('app', 'Admin lists');

$lists = array_unique(ArrayHelper::map($fullData, 'list_name', 'list_name'));
asort($lists, SORT_NATURAL | SORT_FLAG_CASE);
?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'list_name',
            'filter' => $lists
        ],
        'entry_name',
        'description',
        [
            'attribute' => 'weight',
            'headerOptions' => ['width' => '70px'],
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
<div class="button-block">
    <?php if ($searchModel->list_name && !empty($dataProvider)): ?>
        <?= Html::a(Yii::t('app', 'Update Bulk'), ['update-bulk', 'id' => $searchModel->list_name], ['class' => 'btn btn-primary pull-right']) ?>
    <?php else: ?>
        <?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary pull-right']) ?>
    <?php endif ?>
</div>