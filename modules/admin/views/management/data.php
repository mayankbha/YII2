<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 * @var $this yii\web\View
 * @var $load_info array
 * @var $session_info array
 * @var $curr_time string
 */

use yii\grid\GridView;
use yii\helpers\Html;

$toMb = pow(2,20) * 8;
$toTb = pow(2,30) * 8;
?>

<div class="alert-wrap">
    <div class="alert alert-warning alert-dismissible" role="alert">
        <span class="alert-icon">
            <span class="icon"></span>
        </span>
        <div style="line-height: 30px; float: left">
            <b>Server time: <?= Yii::$app->formatter->format($curr_time, 'datetime'); ?></b>
            <i style="color: grey">(Update every 10 seconds)</i>
        </div>
        <?=  Html::a(Yii::t('app', 'Soft reset'), ['reset-soft'], ["aria-hidden" => true, 'data-method' => "post", 'class' => 'btn btn-sm btn-danger', 'style' => 'float: right']) ?>
        <div style="clear: both"></div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">CPU</div>
    <div class="panel-body">
        <?php if (!empty($load_info['cpu_info']) && !empty($load_info['cpu_load'])): ?>
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <?= \simonmesmith\justgage\JustGage::widget([
                        'id' => 'cpu-main-gage',
                        'options' => [
                            'value' => $load_info['cpu_load'],
                            'min' => 0,
                            'max' => 100,
                            'title' => "CPU, %",
                            'startAnimationTime' => 1,
                            'refreshAnimationTime' => 1
                        ]
                    ]); ?>
                </div>
            </div>
            <div class="row">
            <?php foreach ($load_info['cpu_info'] as $name => $value): ?>
                <?php if ($name === '_Total') {
                    continue;
                } ?>
                <div class="col-md-2 col-sm-4 col-xs-6" style="text-align: center">
                    <?= \simonmesmith\justgage\JustGage::widget([
                        'id' => "$name-gage",
                        'options' => [
                            'value' => $value,
                            'min' => 0,
                            'max' => 100,
                            'title' => "Core: $name, %",
                            'startAnimationTime' => 1,
                            'refreshAnimationTime' => 1
                        ],
                        'htmlOptions' => [
                            'style' => 'height:80px;'
                        ]
                    ]); ?>
                </div>
            <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Memory</div>
    <div class="panel-body">
        <?php if (!empty($load_info['mem_info']['ullAvailPageFile']) && !empty($load_info['mem_info']['ullTotalPageFile'])): ?>
            <div class="col-md-4" style="text-align: center">
                <?= \simonmesmith\justgage\JustGage::widget([
                    'id' => 'memory-page_file-gage',
                    'options' => [
                        'value' => round($load_info['mem_info']['ullAvailPageFile'] / $toMb),
                        'min' => 0,
                        'max' => round($load_info['mem_info']['ullTotalPageFile'] / $toMb),
                        'title' => "Memory page file, Mb",
                        'startAnimationTime' => 1,
                        'refreshAnimationTime' => 1
                    ]
                ]); ?>
            </div>
        <?php endif ?>
        <?php if (!empty($load_info['mem_info']['ullAvailPhys']) && !empty($load_info['mem_info']['ullTotalPhys'])): ?>
            <div class="col-md-4" style="text-align: center">
                <?= \simonmesmith\justgage\JustGage::widget([
                    'id' => 'memory-physic-gage',
                    'options' => [
                        'value' => round($load_info['mem_info']['ullAvailPhys'] / $toMb),
                        'min' => 0,
                        'max' => round($load_info['mem_info']['ullTotalPhys'] / $toMb),
                        'title' => "Memory physic, Mb",
                        'startAnimationTime' => 1,
                        'refreshAnimationTime' => 1
                    ]
                ]); ?>
            </div>
        <?php endif ?>
        <?php if (!empty($load_info['mem_info']['ullAvailVirtual']) && !empty($load_info['mem_info']['ullTotalVirtual'])): ?>
            <div class="col-md-4" style="text-align: center">
                <?= \simonmesmith\justgage\JustGage::widget([
                    'id' => 'memory-virtual-gage',
                    'options' => [
                        'value' => round($load_info['mem_info']['ullAvailVirtual'] / $toTb),
                        'min' => 0,
                        'max' => round($load_info['mem_info']['ullTotalVirtual'] / $toTb),
                        'title' => "Memory virtual, Gb",
                        'startAnimationTime' => 1,
                        'refreshAnimationTime' => 1
                    ]
                ]); ?>
            </div>
        <?php endif ?>
    </div>
</div>
<?php if(!empty($session_info)): ?>
    <?= GridView::widget([
        'dataProvider' => (new \yii\data\ArrayDataProvider([
            'allModels' => $session_info,
            'sort' => [
                'attributes' => ['user_id', 'user_name', 'update_time', 'create_time', 'is_logged_in'],
            ]
        ])),
        'layout' => "<div class='table-responsive'>{items}</div>",
        'columns' => [
            ['attribute' => 'user_id', 'label' => 'ID'],
            [
                'attribute' => 'user_name',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a($model['user_name'], \yii\helpers\Url::toRoute(['user/update/', 'id' => $model['user_id']]), ['style' => 'text-decoration: underline']);
                }
            ],
            'user_groups',
            'account_type',
            ['attribute' => 'create_time', 'format' => 'datetime'],
            ['attribute' => 'update_time', 'format' => 'datetime'],
            ['attribute' => 'timeout_value', 'label' => 'Timeout, seconds'],
            ['attribute' => 'live_in_seconds', 'label' => 'Live, seconds'],
            [
                'label' => 'Authorized',
                'format' => 'html',
                'value' => function ($model) {
                    $html = '';
                    $span = function ($value, $text) {
                        $class = ($value) ? 'success' : 'danger';
                        return "<span class='label label-{$class}'>{$text}</span><br />";
                    };

                    if ($model['AuthType.E']['isAuthRequired']) {
                        $html .= $span($model['AuthType.E']['isAuthCompleted'], 'AuthType.E');
                    }
                    if ($model['AuthType.L']['isAuthRequired']) {
                        $html .= $span($model['AuthType.L']['isAuthCompleted'], 'AuthType.L');
                    }
                    if ($model['AuthType.S']['isAuthRequired']) {
                        $html .= $span($model['AuthType.S']['isAuthCompleted'], 'AuthType.S');
                    }
                    if ($model['AuthType.SQ']['isAuthRequired']) {
                        $html .= $span($model['AuthType.SQ']['isAuthCompleted'], 'AuthType.SQ');
                    }

                    return $html;
                }
            ],
        ],
        'tableOptions' => [
            'class' => 'table table-hover table-bordered'
        ],
    ]);
    ?>
<?php endif ?>
