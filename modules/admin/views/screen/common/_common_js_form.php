<?php

use yii\bootstrap\Tabs;
use yii\bootstrap\Html;

?>
<?= Tabs::widget([
    'items' => [
        [
            'label' => 'OnEdit',
            'content' => $this->render('_common_js_templates.php', ['section' => 'js_event_edit']),
            'active' => true,
            'options' => [
                'class' => 'js-generator-section',
                'data-event' => 'js_event_edit',
            ],
        ],
        [
            'label' => 'OnInsert',
            'content' => $this->render('_common_js_templates.php', ['section' => 'js_event_insert']),
            'options' => [
                'class' => 'js-generator-section',
                'data-event' => 'js_event_insert',
            ],
        ],
        [
            'label' => 'onFocusOut',
            'content' => $this->render('_common_js_templates.php', ['section' => 'js_event_change']),
            'options' => [
                'class' => 'js-generator-section',
                'data-event' => 'js_event_change',
            ],
        ],
    ],
    'itemOptions' => ['style' => 'padding: 10px']
]); ?>
