<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 * @var $searchModel \app\modules\admin\models\BaseSearch
 * @var $fullData array
 */

use app\modules\admin\models\forms\AliasForm;

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\CustomLibs;
use app\models\BaseModel;
use yii\data\ArrayDataProvider;

$this->title = Yii::t('app', 'Alias Relationship');

?>

<h1><?= $this->title ?></h1>

<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'ParentTable',
            //'filter' => $result
        ],
        [
            'attribute' => 'ParentField',
            //'filter' => $dependency_type
        ],
        [
            'attribute' => 'ChildTable',
            //'filter' => $result
        ],
        [
            'attribute' => 'ChildField',
            //'filter' => $dependency_type
        ],
		[
			'class' => 'yii\grid\ActionColumn',
			'headerOptions' => ['width' => '70px'],
			'template' => '{update} {delete}',
			'buttons' => [
                'update' => function ($url, $fullData) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span>',
                        Url::toRoute(['/admin/alias-relationship/update?id='.$fullData['ChildTable'].';'.$fullData['ChildField'].';'.$fullData['ParentTable'].';'.$fullData['ParentField']])
                    );
                },
				'delete' => function ($url, $fullData) {
					return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['/admin/alias-relationship/delete', 'id' => $fullData['ChildTable'].';'.$fullData['ChildField'].';'.$fullData['ParentTable'].';'.$fullData['ParentField']]), [
						'class' => '',
						'data' => [
							'confirm' => 'Are you sure you want to delete?',
							'method' => 'post',
						],
					]);
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
	<?= Html::a(Yii::t('app', 'Back'), Url::toRoute('/admin/alias-dependency'), ['class' => 'btn btn-link']); ?>

    <?php /*
        $buttonLabel = Yii::t('app', 'Paged View');
        $buttonLink = ['index'];
        if($searchModel->_limit === $searchModel::PAGINATION_LIMIT) {
            $buttonLabel = Yii::t('app', 'Show All');
            $buttonLink['showall'] = true;
        }
    ?>

    <?= Html::a($buttonLabel, $buttonLink, ['class' => 'btn btn-outline pull-right', 'id' => 'viewMode']) */ ?>

	<?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['/admin/alias-relationship/create']), ['class' => 'btn btn-primary pull-right']) ?>
</div>
