<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $tabModel \app\models\Screen
 */

use app\models\UserAccount;
use app\components\StepperWidget;
use app\models\Screen;

$hasActiveTab = 'active';
?>
<?= (in_array(UserAccount::getMenuViewType(), [UserAccount::MENU_VIEW_TWO_LEVEL, UserAccount::MENU_VIEW_LEFT_BAR])) ? '<div style="overflow-x: auto">' : ''; ?>
<div class="btn-group nav-right-group" role="group" data-toggle="tab">
    <?php foreach ($tabModel->list as $item): ?>
        <?php $screenTemplate = Screen::decodeTemplate($item['screen_tab_template'], true); ?>
        <button data-target="#<?= $item['screen_name'] . '_' . $item['id']; ?>" data-tab-id="<?= $item['id']; ?>"
                data-lib="<?= $item['screen_lib']; ?>"
                class="screen-tab btn btn-default <?= $hasActiveTab ?>"
                data-toggle="tab"
                <?= ($screenTemplate->alias_framework->enable && $screenTemplate->alias_framework->transaction_request) ? 'data-alias-framework="' . $screenTemplate->alias_framework->transaction_request .'"' : '' ?>>
            <span title="<?= $item['screen_tab_text']; ?>"><?= $item['screen_tab_text']; ?></span>
        </button>
        <?php $hasActiveTab = ''; ?>
    <?php endforeach; ?>
</div>
<?= (in_array(UserAccount::getMenuViewType(), [UserAccount::MENU_VIEW_TWO_LEVEL, UserAccount::MENU_VIEW_LEFT_BAR])) ? '</div>' : ''; ?>

<div class="tab-content">
    <?= StepperWidget::widget(['config' => $tabModel->tplData, 'active_id' => !empty($tabModel->list[0]['id']) ? $tabModel->list[0]['id'] : null]) ?>

    <?php $hasActiveTab = 'in active'; ?>
    <?php foreach ($tabModel->list as $item): ?>
        <div id="<?= "{$item['screen_name']}_{$item['id']}" ?>" data-section-lib="<?= $item['screen_lib']; ?>" class="tab-pane fade <?= $hasActiveTab ?>">
        </div>
        <?php $hasActiveTab = ''; ?>
    <?php endforeach; ?>
</div>