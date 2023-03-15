<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\AliasForm */
use yii\bootstrap\Tabs;
use app\modules\admin\models\forms\AliasForm;

$tabs = [];
$this->title = Yii::t('app', 'Create Alias');
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<?php $tabs[0] = '<div class="border-form-block">
    '.$this->render('form', ['model' => $model]).'
</div> <p><br />&nbsp;</p>';

$tabs[1] = $this->render('formtab2', ['request' => $request]);
$tabs[2] = $this->render('formtab3', ['request' => $request]);
$tabs[3] = $this->render('formtab4', ['request' => $request]);
$tabs[4] = $this->render('formtab5', ['request' => $request]);

$tabTypesArr = AliasForm::tabLabels();
$t = 0;  
foreach ($tabTypesArr as $key => $value) {
	$tabTypes[] = [
            'label' => $value,
            'content' => $tabs[$t],
            'options' => ['id' => 'tab_'.$key, 'key' => $key],
            'active' => ($t==0?true:false)
        ];
    $t++;
}

echo Tabs::widget([
    'items' => $tabTypes,
]);