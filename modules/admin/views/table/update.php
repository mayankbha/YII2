<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Update Table');

?>

<h1><?= $this->title ?></h1>

<div class="button-block">
    <?= Html::a(Yii::t('app', 'Download Example CSV'), Url::toRoute('/admin/table/download-csv/'.$model->table_name), ['class' => 'btn btn-primary']); ?>

   	<button class="btn btn-primary" data-toggle="modal" data-target="#upload-csv-modal" data-url="<?php echo Url::to(['/admin/table/upload-csv'], true); ?>" type="button">Upload CSV</button>

	<br><br>
</div>

<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
	<?= $this->render('form', ['model' => $model, 'tables' => $tables, 'dataTypes' => $dataTypes]); ?>
</div>

<?php echo $this->render('upload-csv-modal', ['model' => $model]); ?>