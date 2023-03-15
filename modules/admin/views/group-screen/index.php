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

use app\modules\admin\models\forms\GroupScreenForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Admin group screen');

$groupNames = array_unique(ArrayHelper::map($fullData, 'group_name', 'group_name'));
$menuNames = array_filter(array_unique(ArrayHelper::map($fullData, 'menu_name', 'menu_name')));

asort($groupNames, SORT_NATURAL | SORT_FLAG_CASE);
asort($menuNames, SORT_NATURAL | SORT_FLAG_CASE);
?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'columns' => [
        [
            'attribute' => 'add',
            'format' => 'html',
            'filter' => GroupScreenForm::$boolProperty,
            'value' => function ($data) {
                switch ($data['add']) {
                    case GroupScreenForm::BOOL_API_TRUE:
                        return '<span class="label label-success">' . GroupScreenForm::$boolProperty[$data['add']] . '</span>';
                        break;
                    default:
                        return '<span class="label label-danger">' . GroupScreenForm::$boolProperty[$data['add']] . '</span>';
                        break;
                }
            },
            'headerOptions' => ['width' => '90px']
        ],
        [
            'attribute' => 'delete',
            'format' => 'html',
            'filter' => GroupScreenForm::$boolProperty,
            'value' => function ($data) {
                switch ($data['delete']) {
                    case GroupScreenForm::BOOL_API_TRUE:
                        return '<span class="label label-success">' . GroupScreenForm::$boolProperty[$data['delete']] . '</span>';
                        break;
                    default:
                        return '<span class="label label-danger">' . GroupScreenForm::$boolProperty[$data['delete']] . '</span>';
                        break;
                }
            },
            'headerOptions' => ['width' => '90px']
        ],
        [
            'attribute' => 'edit',
            'format' => 'html',
            'filter' => GroupScreenForm::$boolProperty,
            'value' => function ($data) {
                switch ($data['edit']) {
                    case GroupScreenForm::BOOL_API_TRUE:
                        return '<span class="label label-success">' . GroupScreenForm::$boolProperty[$data['edit']] . '</span>';
                        break;
                    default:
                        return '<span class="label label-danger">' . GroupScreenForm::$boolProperty[$data['edit']] . '</span>';
                        break;
                }
            },
            'headerOptions' => ['width' => '90px']
        ],
        [
            'attribute' => 'copy',
            'format' => 'html',
            'filter' =>  GroupScreenForm::$boolProperty,
            'value' => function ($data) {
                switch ($data['copy']) {
                    case GroupScreenForm::BOOL_API_TRUE:
                        return '<span class="label label-success">' . GroupScreenForm::$boolProperty[$data['copy']=='Y' ? 'Y' : 'N'] . '</span>';
                        break;
                    default:
                        return '<span class="label label-danger">' . GroupScreenForm::$boolProperty[$data['copy']=='Y' ? 'Y' : 'N'] . '</span>';
                        break;
                }
            },
            'headerOptions' => ['width' => '90px']
        ],
        [
            'attribute' => 'inquire',
            'format' => 'html',
            'filter' => GroupScreenForm::$boolProperty,
            'value' => function ($data) {
                switch ($data['inquire']) {
                    case GroupScreenForm::BOOL_API_TRUE:
                        return '<span class="label label-success">' . GroupScreenForm::$boolProperty[$data['inquire']] . '</span>';
                        break;
                    default:
                        return '<span class="label label-danger">' . GroupScreenForm::$boolProperty[$data['inquire']] . '</span>';
                        break;
                }
            },
            'headerOptions' => ['width' => '90px']
        ],
        [
            'attribute' => 'execute',
            'format' => 'html',
            'filter' => GroupScreenForm::$boolProperty,
            'value' => function ($data) {
                switch ($data['execute']) {
                    case GroupScreenForm::BOOL_API_TRUE:
                        return '<span class="label label-success">' . GroupScreenForm::$boolProperty[$data['execute']] . '</span>';
                        break;
                    default:
                        return '<span class="label label-danger">' . GroupScreenForm::$boolProperty[$data['execute']] . '</span>';
                        break;
                }
            },
            'headerOptions' => ['width' => '90px']
        ],
        [
            'attribute' => 'group_name',
            'filter' => $groupNames
        ],
        [
            'attribute' => 'menu_name',
            'filter' => $menuNames
        ],
        'screen_text',
        'weight',
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
    <?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary pull-right']) ?>
</div>