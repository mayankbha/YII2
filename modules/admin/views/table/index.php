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

use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Tables');

//echo "<pre>"; print_r($dataProvider); die;

?>

<h1><?= Yii::t('app', 'Table') ?></h1>

<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'table_name',
            'filter' => array_unique(ArrayHelper::map($fullData, 'table_name', 'table_name'))
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '70px'],
            'template' => '{update} {delete}',
			'buttons' => [
                        'update' => function ($url, $model, $key) use ($dataProvider) {
							$urlarr = explode("/", $url);
                            $offset = count($urlarr) - 1;
                            $offset = ($offset < 0 ? 0 : $offset);

                            return Html::a('', Url::toRoute(['table/update/'.$dataProvider->allModels[$urlarr[$offset]]['table_name']]), ['class' => 'glyphicon glyphicon-pencil', 'title' => Yii::t('app', 'Edit')]);
                        },
						'delete' => function ($url, $model, $key) use ($dataProvider) {
							$urlarr = explode("/", $url);
                            $offset = count($urlarr) - 1;
                            $offset = ($offset < 0 ? 0 : $offset);

							$fullURL = $url;

							if (isset($dataProvider->allModels[$urlarr[$offset]]['table_name'])) {
                                $id = $dataProvider->allModels[$urlarr[$offset]]['table_name'];
                                $url = str_replace($urlarr[$offset], $id, $fullURL);
                            }

							return Html::a('', $url, ['class' => 'glyphicon glyphicon-trash', 'title' => Yii::t('app', 'Delete')]);

                            return Html::a('', Url::toRoute(['table/delete/'.$dataProvider->allModels[$urlarr[$offset]]['table_name']]), ['class' => 'glyphicon glyphicon-remove', 'title' => Yii::t('app', 'Delete')]);
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
    <?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary pull-right']) ?>
</div>