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

use app\modules\admin\models\forms\UserForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Admin users');

$groupAreas = array_filter(array_unique(ArrayHelper::map($fullData, 'group_area', 'group_area')));
asort($groupAreas, SORT_NATURAL | SORT_FLAG_CASE);

?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        'user_name',
        [
            'attribute' => 'group_area',
            'format' => 'html',
            'filter' => $groupAreas,
            'value' => function ($model) {
                return ($explode = explode(';', $model['group_area'])) ? implode(',<br />',
                    $explode) : $model['group_area'];
            }

        ],
        [
            'attribute' => 'email',
            'format' => 'email'
        ],
        'account_type',
        [
            'attribute' => 'account_status',
            'format' => 'html',
            'filter' => UserForm::$statusProperty,
            'value' => function ($model) {
                switch ($model['account_status']) {
                    case UserForm::ACTIVE_ACCOUNT:
                        return '<span class="label label-success">' . UserForm::$statusProperty[$model['account_status']] . '</span>';
                        break;
                    case UserForm::INACTIVE_ACCOUNT:
                        return '<span class="label label-danger">' . UserForm::$statusProperty[$model['account_status']] . '</span>';
                        break;
                }
            },
            'headerOptions' => ['width' => '100px']
        ],
        'account_name',
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '70px'],
            'template' => '{copy_user} {update} {delete}',
			'buttons' => [
                'copy_user' => function ($url, $model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-copy"></span>',
                        Url::toRoute(['user/update/'.$model['pk'].'?action=copy_user']),
                        [
                            'title' => Yii::t('app', 'Copy User'),
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
    <?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary pull-right']) ?>
</div>