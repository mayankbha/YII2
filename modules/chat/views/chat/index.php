<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 * @var $roomOwners array
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\chat\models\Room;

$this->title = Yii::t('app', 'Chat rooms');
?>

<h1><?= Yii::t('app', 'Chat rooms') ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class=\"table-responsive activity-table\">{items}</div>{pager}",
    'columns' => [
        [
            'attribute' => 'owner',
            'format' => 'html',
            'value' => function ($data) use ($roomOwners) {
                if (!empty($roomOwners[$data['owner']])) {
                    return Html::a($roomOwners[$data['owner']], ['/admin/user/update', 'id' => $data['owner']]);
                }

                return 'User not fount';
            }
        ],
        'room_name',
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