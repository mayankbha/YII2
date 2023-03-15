<?php

use yii\bootstrap\Tabs;
use conquer\codemirror\CodemirrorWidget;

?>
<?= Tabs::widget([
    'options' => [
        'onclick' =>'fieldsConstructor.refreshCodeMirror(0)',
    ],
    'items' => [
        [
            'label' => 'OnEdit',
            'content' => CodemirrorWidget::widget([
                'name' => 'js_event_edit',
                'id' => 'js_edit_' . rand(),
                'preset' => 'javascript',
                'value' => '',
                'settings' => ['tabindex' => 1]
            ]),
            'active' => true,
        ],
        [
            'label' => 'OnInsert',
            'content' => CodemirrorWidget::widget([
                'name' => 'js_event_insert',
                'id' => 'js_edit_' . rand(),
                'preset' => 'javascript',
                'value' => '',
                'settings' => ['tabindex' => 2]
            ])
        ],
        [
            'label' => 'onFocusOut',
            'content' => CodemirrorWidget::widget([
                'name' => 'js_event_change',
                'id' => 'js_edit_' . rand(),
                'preset' => 'javascript',
                'value' => '',
                'settings' => ['tabindex' => 3]
            ]),
            'headerOptions' => ['class' => 'js-custom-onchange-tab']
        ],
    ],
    'itemOptions' => ['style' => 'padding: 0', 'class'=>'custom-js-stab']
]); ?>
