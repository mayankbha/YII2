<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 * @var $searchModel \app\modules\admin\models\BaseSearch
 * @var $fullData array
 */

use app\modules\admin\models\forms\JobSchedulerForm;
 
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\GetListList;

$this->title = Yii::t('app', 'Job Scheduler');

?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        'job_name',
		'job_description',
		'launch_type',
		'launch_condition',
		[
            'attribute' => 'is_active',
            'format' => 'html',
            'filter' => JobSchedulerForm::$types,
            'value' => function ($model) {
                switch ($model['is_active']) {
                    case JobSchedulerForm::TYPE_ACTIVE:
                        return '<span class="label label-success">' . JobSchedulerForm::$types[$model['is_active']] . '</span>';
                        break;
                    case JobSchedulerForm::TYPE_INACTIVE:
                        return '<span class="label label-danger">' . JobSchedulerForm::$types[$model['is_active']] . '</span>';
                        break;
                }
            },
            'headerOptions' => ['width' => '100px']
        ],
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

<div class="button-block screen-manage">
	<?= Html::a(Yii::t('app', 'Create with builder'), Url::toRoute(['builder']), [
			'class' => 'btn btn-outline',
			'style' => 'margin: 0 10px',
			'title' => Yii::t('app', 'Create with constructor')
		]) ?>

	<?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary']) ?>
</div>
