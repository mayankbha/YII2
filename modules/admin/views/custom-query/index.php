<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 * @var $searchModel \app\modules\admin\models\BaseSearch
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Documents family');
?>

<h1><?= Yii::t('app', 'Custom query') ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        'query_name',
        'description',
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
    <?= Html::a(Yii::t('app', 'Create with builder'), Url::toRoute(['builder']), ['class' => 'btn btn-outline']) ?>
    <?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary']) ?>
</div>