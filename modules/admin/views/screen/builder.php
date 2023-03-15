<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $model ScreenForm
 * @var $pullUpList array
 */

use app\modules\admin\models\forms\ScreenForm;
use app\models\GetListList;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Builder step 2');
$screenType = ScreenForm::$types[$model->screen_tab_template->layout_type];
if ($baseLanguages = GetListList::getByNames([GetListList::BASE_NAME_LANGUAGE])) {
    $baseLanguages = ArrayHelper::map($baseLanguages[GetListList::BASE_NAME_LANGUAGE], 'description', 'entry_name');
}
?>

<div class="builder-constructor">
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="policy_dashboard">
            <div style="min-height: 704px;">
                <div id="new-tab-block">
                    <div class="clearfix">

                        <?php if ($screenType['header']): ?>
                            <div class="alert alert-warning header-section section-panel" data-row="0" data-col="0"
                                 style="position: relative">
                                <div class="header-section-content panel-body">
                                    <div class="loader"></div>
                                </div>
                                <span class="panel-controls">
                                    <span class="glyphicon glyphicon-collapse-down fields-button-config" style="display: none;"></span>
                                    <span class="glyphicon glyphicon-comment fields-label-config" style="display: none;"></span>
                                    <span class="glyphicon glyphicon-import fields-constructor-btn"
                                          data-toggle="modal"
                                          data-target="#fields-modal" aria-hidden="true"
                                          style="display: none;"></span>
                                    <span class="glyphicon glyphicon-cog setting-icon" data-row="0" data-col="0"
                                          data-toggle="modal" data-target="#setting-modal" aria-hidden="true"
                                          title="<?= Yii::t('app', 'Setting section') ?>"
                                          style="display: none;"></span></span>
                            </div>
                        <?php endif ?>

                        <div class="stats-section">
                            <?php $colWidth = ($screenType['col_count'] == 1) ? 12 : 6; ?>
                            <?php for ($i = 1; $i <= $screenType['row_count']; $i++): ?>
                                <div class="row">
                                    <?php for ($j = 1; $j <= $screenType['col_count']; $j++): ?>
                                        <div class="col-sm-<?= $colWidth ?>">
                                            <div class="panel panel-default panel-window section-panel"
                                                 data-row="<?= $i ?>" data-col="<?= $j ?>" data-type="chart">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><?= Yii::t('app', 'New section') ?></h3>
                                                    <span class="panel-controls">
                                                        <span class="glyphicon glyphicon-collapse-down fields-button-config" style="display: none;"></span>
                                                        <span class="glyphicon glyphicon-comment fields-label-config" style="display: none;"></span>
                                                        <span class="glyphicon glyphicon-import fields-constructor-btn"
                                                              data-toggle="modal" data-target="#fields-modal"
                                                              aria-hidden="true"
                                                              style="display: none;"></span>
                                                        <span class="glyphicon glyphicon-cog setting-icon"
                                                              data-row="<?= $i ?>" data-col="<?= $j ?>"
                                                              data-toggle="modal" data-target="#setting-modal"
                                                              aria-hidden="true"
                                                              style="display: none;"></span>
                                                    </span>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="loader"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor ?>
                                </div>
                            <?php endfor ?>
                        </div>

                    </div>
                </div>
            </div>

            <div class="btn-group nav-right-group" role="group">
                <button type="button" class="btn btn-default"><?= $model->screen_tab_text ?></button>
            </div>
        </div>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'new-screen-create',
        'action' => ($model->id) ? Url::toRoute(['update', 'id' => $model->id]) : Url::toRoute(['create']),
        'fieldConfig' => [
            'template' => '{input}',
            'options' => ['tag' => false]
        ],
    ]); ?>
    <?= $form->field($model, 'screen_tab_devices')->checkboxList($model::$devices, ['style' => 'display: none;']); ?>
    <?= $form->field($model, 'screen_name')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'screen_lib')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'screen_tab_text')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'screen_desc')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'screen_tab_weight')->hiddenInput()->label(false); ?>
    <?php
    $model->screen_tab_template = json_encode($model->screen_tab_template);
    echo $form->field($model, 'screen_tab_template')->hiddenInput(['id' => 'form-template-layout'])->label(false);
    ?>
    <div class="form-group row pull-right" style="margin-right: 0">
        <?= Html::a(Yii::t('app', 'Back'), Url::current(['return' => true]), ['class' => 'btn btn-link']); ?>
        <?php
        if ($model->id) echo Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary pull-right']);
        else echo Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary pull-right']);
        ?>
    </div>
    <?php ActiveForm::end() ?>
</div>

<?= $this->render('common/build-modal', ['model' => $model]); ?>
<?= $this->render('common/fields-modal'); ?>
<?= $this->render('common/internationalization-modal', ['languages' => $baseLanguages]); ?>
<?= $this->render('common/table-modal'); ?>
<?= $this->render('common/pullup-modal', ['pullUpList' => $pullUpList]); ?>
<?= $this->render('common/formatting-modal', ['formClass' => 'table-constructor-form', 'modalID' => 'formatting-modal-table']); ?>
<?= $this->render('common/formatting-modal', ['formClass' => 'section-formatting-form', 'modalID' => 'formatting-modal-section']); ?>
<?= $this->render('common/formatting-modal', ['formClass' => 'fields-constructor-form', 'modalID' => 'formatting-modal']); ?>
<?= $this->render('common/extensions-modal'); ?>
<?= $this->render('common/execute-function-modal', ['model' => $model]); ?>

<?= $this->render('common/js-edit-modal', ['formClass' => 'fields-constructor-form', 'modalID' => 'js-edit-modal', 'jsTemplates' => $jsTemplates]); ?>
<?= $this->render('common/js-edit-modal', ['formClass' => 'table-constructor-form', 'modalID' => 'js-edit-table', 'jsTemplates' => $jsTemplates]); ?>

<?= $this->render('common/access-modal', ['formClass' => 'table-constructor-form', 'modalID' => 'access-modal-table']); ?>
<?= $this->render('common/access-modal', ['formClass' => 'fields-constructor-form', 'modalID' => 'access-modal']); ?>
