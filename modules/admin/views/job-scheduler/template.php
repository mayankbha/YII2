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

<div class="cf sub-content-wrapper">
    <div style="position: relative">
        <!-- Tab panes -->
        <div class="tab-content" style="min-height: 700px; position: relative;">
            <div class="screen-group-tab tab-pane active">
                <?= $this->render('element', ['tabModel' => $tabModel]); ?>
            </div>
        </div>
    </div>
</div>
