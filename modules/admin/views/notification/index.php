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
use app\models\GetListList;

$this->title = Yii::t('app', 'Notifications');
$baseLists = GetListList::getArrayForSelectByNames(GetListList::$notifyListName);
?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
    'filterModel' => $searchModel,
    'columns' => [
        'notify_name',
        [
            'attribute' => 'notify_type',
            'filter' => $baseLists[GetListList::BASE_NAME_NOTIFY_TYPE],
            'value' => function ($model) use ($baseLists) {
                if (isset($baseLists[GetListList::BASE_NAME_NOTIFY_TYPE][$model['notify_type']])) {
                    return $baseLists[GetListList::BASE_NAME_NOTIFY_TYPE][$model['notify_type']];
                }

                return $model['notify_type'];
            }
        ],
        [
            'attribute' => 'recipient_type',
            'filter' => $baseLists[GetListList::BASE_NAME_NOTIFY_RECIPIENT_TYPE],
            'value' => function ($model) use ($baseLists) {
                if (isset($baseLists[GetListList::BASE_NAME_NOTIFY_RECIPIENT_TYPE][$model['recipient_type']])) {
                    return $baseLists[GetListList::BASE_NAME_NOTIFY_RECIPIENT_TYPE][$model['recipient_type']];
                }

                return $model['recipient_type'];
            }
        ],
        [
            'attribute' => 'recipient_list',
            'format' => 'html',
            'value' => function ($model) {
                $idList = explode(';', $model['recipient_list']);
                $result = [];

                foreach($idList as $id) {
                    if ($model['recipient_type'] == 'NotifyRecipientType.G') {
                        $result[] = Html::a($id, ['/admin/group/update', 'id' => $id]);
                    } elseif ($model['recipient_type'] == 'NotifyRecipientType.U') {
                        $result[] = Html::a($id, ['/admin/user/update', 'id' => $id]);
                    }
                }

                return implode(', ', $result);
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['width' => '70px'],
            'template' => '{update} {delete}'
        ]
    ],
    'tableOptions' => [
        'class' => 'table table-hover'
    ],
]);
?>
<div class="button-block"><?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary pull-right']) ?></div>