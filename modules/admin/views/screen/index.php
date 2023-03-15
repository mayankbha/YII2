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

$this->title = Yii::t('app', 'Admin screen');

$screenNames = array_unique(ArrayHelper::map($fullData, 'screen_name', 'screen_name'));
$menuNames = array_filter(array_unique(ArrayHelper::map($fullData, 'menu_name', 'menu_name')));
$screenLibs = array_unique(ArrayHelper::map($fullData, 'screen_lib', 'screen_lib'));

asort($screenNames, SORT_NATURAL | SORT_FLAG_CASE);
asort($menuNames, SORT_NATURAL | SORT_FLAG_CASE);
asort($screenLibs, SORT_NATURAL | SORT_FLAG_CASE);
?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'menu_name',
            'filter' => $menuNames
        ],
        [
            'attribute' => 'screen_name',
            'filter' => $screenNames
        ],
        'screen_desc',
        'screen_tab_text',
        [
            'attribute' => 'screen_lib',
            'filter' => $screenLibs
        ],
        'screen_tab_devices',
        'screen_tab_weight',
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '70px'],
            'template' => '{builder} {update} {delete}',
            'buttons' => [
                'builder' => function ($url, $model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-cog"></span>',
                        Url::toRoute(['builder', 'id' => $model['pk']]),
                        [
                            'title' => Yii::t('app', 'Update with builder'),
                            'style' => 'color: #fab01b !important'
                        ]
                    );
                }
            ]
        ]
    ],
    'tableOptions' => [
        'class' => 'table table-hover'
    ],
]);
?>

<div class="button-block">
    <?= Html::a(Yii::t('app', 'Create with builder'), Url::toRoute(['builder']), [
        'class' => 'btn btn-outline',
        'style' => 'margin: 0 10px',
        'title' => Yii::t('app', 'Create with constructor')
    ]) ?>
    <?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary']) ?>
</div>