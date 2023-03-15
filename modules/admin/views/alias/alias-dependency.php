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

$this->title = Yii::t('app', 'Alias Dependency');

//echo "<pre>"; print_r($dataProvider);
//echo "<pre>"; print_r($searchModel);

?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
    //'filterModel' => $searchModel,
    'columns' => [
        'AliasTable',
        'AliasType',
		'RequestTable',
		'DependencyType',
		[
			'class' => 'yii\grid\ActionColumn',
			'headerOptions' => ['width' => '70px'],
			'template' => '{update} {delete}',
			'buttons' => [
                'update' => function ($url, $fullData) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span>',
                        Url::toRoute(['alias/alias-dependency?action=update&id='.$fullData['AliasType'].';'.$fullData['AliasTable'].';'.$fullData['DependencyType']])
                    );
                },
				'delete' => function ($url, $fullData) {
					return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['delete-alias-dependency', 'id' => $fullData['AliasType'].';'.$fullData['AliasTable'].';'.$fullData['DependencyType']]), [
						'class' => '',
						'data' => [
							'confirm' => 'Are you sure you want to delete?',
							'method' => 'post',
						],
					]);
                    /*return Html::a(
                        '<span class="glyphicon glyphicon-trash"></span>',
                        Url::toRoute(['delete-alias-dependency', 'id' => $fullData['AliasType'].';'.$fullData['AliasTable'].';'.$fullData['DependencyType']])
                    );*/
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
	<?= Html::a(Yii::t('app', 'Back'), Url::toRoute('/admin/alias'), ['class' => 'btn btn-link']); ?>

	<?= Html::a(Yii::t('app', 'Manage Alias Relationship'), Url::toRoute('alias-relationship?action=list&id=0'), ['class' => 'btn btn-primary']); ?>

	&nbsp;&nbsp;&nbsp;&nbsp;

	<?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['alias/alias-dependency?action=create&id=0']), ['class' => 'btn btn-primary pull-right']) ?>
</div>