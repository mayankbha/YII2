<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\admin\models\forms\ImageForm;
/**
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 * @var $searchModel \app\modules\admin\models\BaseSearch
 * @var $fullData array
 */
$this->title = Yii::t('app', 'Admin logo');
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        'list_name',
        'entry_name',
        [
            'attribute' => 'type',
            'filter' => ImageForm::$types
        ],
        'description',
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '70px'],
            'template' => '{update} {delete}',
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

