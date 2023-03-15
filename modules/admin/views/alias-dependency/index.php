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

use app\modules\admin\models\forms\AliasDependencyUserForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\models\GetAliasList;
use app\models\GetAliasInfo;

$data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetTablesInfo", ""));

foreach ($data as $record) {
    $result[] = $record['table_name'];
}

sort($result);

if (($aliasList = GetAliasInfo::getData([],[],['field_out_list' => ['AliasDatabaseTable']])) && !empty($aliasList->list)) {
    $aliasList = $aliasList->list;
}

$alias_type_list = GetAliasList::getAliasTypesDropdown();

$final_alias_type = array();

foreach($alias_type_list as $alias_type) {
	if($alias_type == 'Database Field' || $alias_type == 'Array') {
		if($alias_type == 'Database Field')
			$alias_type = 'Alias';

		$final_alias_type[$alias_type] = $alias_type;
	}
}

$dependency_type = array('PKS' => 'PKS', 'REL' => 'REL');

$this->title = Yii::t('app', 'Alias Dependency');

?>

<h1><?= $this->title ?></h1>

<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'AliasTable',
            'filter' => $result
        ],
        [
            'attribute' => 'AliasType',
            'filter' => $final_alias_type
        ],
        [
            'attribute' => 'RequestTable',
            'filter' => $result
        ],
        [
            'attribute' => 'DependencyType',
            'filter' => $dependency_type
        ],
        [
			'class' => 'yii\grid\ActionColumn',
			'headerOptions' => ['width' => '70px'],
			'template' => '{update} {delete}',
			'buttons' => [
                'update' => function ($url, $fullData) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span>',
                        Url::toRoute(['/admin/alias-dependency/update?id='.$fullData['AliasType'].';'.$fullData['AliasTable'].';'.$fullData['DependencyType']])
                    );
                },
				'delete' => function ($url, $fullData) {
					return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['/admin/alias-dependency/delete', 'id' => $fullData['AliasType'].';'.$fullData['AliasTable'].';'.$fullData['DependencyType']]), [
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
	<?= Html::a(Yii::t('app', 'Back'), Url::toRoute('/admin/alias'), ['class' => 'btn btn-link']); ?>

    <?php /*
        $buttonLabel = Yii::t('app', 'Paged View');
        $buttonLink = ['index'];
        if($searchModel->_limit === $searchModel::PAGINATION_LIMIT) {
            $buttonLabel = Yii::t('app', 'Show All');
            $buttonLink['showall'] = true;
        }
    ?>

    <?= Html::a($buttonLabel, $buttonLink, ['class' => 'btn btn-outline pull-right', 'id' => 'viewMode']) */ ?>

	<?= Html::a(Yii::t('app', 'Manage Alias Relationship'), Url::toRoute('/admin/alias-relationship'), ['class' => 'btn btn-primary']); ?>

	&nbsp;&nbsp;&nbsp;&nbsp;

	<?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['/admin/alias-dependency/create']), ['class' => 'btn btn-primary pull-right']) ?>
</div>