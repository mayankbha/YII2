<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ArrayDataProvider
 * @var $searchModel \app\modules\admin\models\BaseSearch
 * @var $fullData array
 */ 
if(!isset($_SESSION['screenData']['sessionData']['sessionhandle']))
	$this->redirect(array('login'));

use yii\grid\GridView;
use yii\bootstrap\Tabs;
use yii\helpers\ArrayHelper;
use yii\helpers\Html; 
use yii\helpers\Url;
use app\models\GetAliasList;

/* echo "<pre>".Url::base()."</pre>";
echo "<pre>".$this->context->action->id."</pre>"; */
//echo "<pre>".$_SESSION['screenData']['sessionData']['sessionhandle']."</pre>";
$this->title = Yii::t('app', 'Alias Management');
?>
<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>
 <div class="alert alert-warning alert-dismissible" role="alert">
        <span class="alert-icon">
            <span class="icon"></span>
        </span>
        <div class="notice" style="line-height: 30px; float: left; width: 90% ">
            This will check for database changes and create new aliases accordingly. It will also reload the existing aliases and do a soft reset to the server.
            <i style="color: grey"></i>
        </div>
        <button id="reloadAliases" class="btn btn-sm btn-danger" aria-hidden="" style="float: right">Reload aliases</button>        <div style="clear: both"></div>
    </div>

	<a class="btn btn-sm btn-primary" style="float: right;" href="<?php echo Url::toRoute(['/admin/alias-dependency']); ?>">Manage dependency for database aliases</a><div style="clear: both"></div>
<?php 
$dismiss = false;
$session = Yii::$app->session;
$count_types = [];
$grid_counts = [];
$grid_page_counts = [];
$grids = [];
$aliasTypes = GetAliasList::getAliasTypes();
//$prevUrl = Url::toRoute(['site/delete-data']);
$g = 0; 
foreach($aliasTypes as $AliasType){
	$current_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
	$limit = 10; 
	if ($session->has('count_types') == false){
		$getAllFormatTypes = GetAliasList::callAPI("GetAliasList", $AliasType, 1, 99999999, "AliasFormatType");
		$jsonToArray = GetAliasList::jsonToArray($getAllFormatTypes); $formatRaw = []; 
		/* if(empty($jsonToArray)){
			echo "<pre>API is not connected. Reconnection attempt # $g</pre>"; 
			$dismiss = true; 
		} */
		for($j=0;$j<count($jsonToArray);$j++){
			$formatRaw[] = $jsonToArray[$j]["AliasFormatType"];
		}
		$count_types[] = array_count_values($formatRaw);
	}
	else{
		if(!empty($count_types[$g])){
			$count_types[] = $session->get('count_types')[$g];
		}
		else{
			$getAllFormatTypes = GetAliasList::callAPI("GetAliasList", $AliasType, 1, 99999999, "AliasFormatType");
			$jsonToArray = GetAliasList::jsonToArray($getAllFormatTypes); $formatRaw = []; 
			for($j=0;$j<count($jsonToArray);$j++){
				$formatRaw[] = $jsonToArray[$j]["AliasFormatType"];
			}
			$count_types[] = array_count_values($formatRaw);
		}
	}
	
	//echo "<pre>".print_r($count_types)."</pre>";
	//$getIndividual = GetAliasList::callAPI("GetAliasList", [["Alias.AccountingDaily.AcctAmount"], [$AliasType]], $current_page, $limit, "", ["AliasCode", "AliasType"]);
	
	//if(!empty($jsonToArray)){ 
		if ($session->has('grid_page_counts')){
			if(!empty($session->get('grid_page_counts'))){
				$total_records_for_database_fields = $session->get('grid_counts')[$g];
				$page_total = $session->get('grid_page_counts')[$g];
			} else {
				$json = GetAliasList::callAPI("GetAliasList", $AliasType, 1, 99999999, "AliasFormat");
				$jsonToArray = GetAliasList::jsonToArray($json);
				$total_records_for_database_fields = count($jsonToArray);
				$page_total = round($total_records_for_database_fields / $limit);
				$grid_counts[] = $total_records_for_database_fields;
				$grid_page_counts[] = $page_total;
			}
		}
		else{ 
			$json = GetAliasList::callAPI("GetAliasList", $AliasType, 1, 99999999, "AliasFormat");
			$jsonToArray = GetAliasList::jsonToArray($json);
			$total_records_for_database_fields = count($jsonToArray);
			$page_total = round($total_records_for_database_fields / $limit);
			$grid_counts[] = $total_records_for_database_fields;
			$grid_page_counts[] = $page_total;		
		}
		//echo "<pre>".($page_total<$limit?1:$page_total)."</pre>";
		$pager = GetAliasList::customPager(($total_records_for_database_fields<$limit?1:$page_total), $limit, [], $g);  
		$json = GetAliasList::callAPI("GetAliasList", $AliasType, $current_page, $limit);
		$arrayDataProvider = GetAliasList::generateProvider($json);
		array_push($grids, GridView::widget(GetAliasList::generateGridArray($arrayDataProvider, $searchModel, $fullData, $count_types[$g], $json, $AliasType)).$pager); 
	//}
	$g++;
}
//exit;
if($dismiss == false):

$session->set('count_types', $count_types);
$session->set('grid_counts', $grid_counts);
$session->set('grid_page_counts', $grid_page_counts);

echo Tabs::widget([
    'items' => [
        [
            'label' => 'Database Fields',
            'content' => $grids[0],
            'options' => ['id' => 'grid_alias', 'data-alias_type' => $aliasTypes[0]],
            'active' => true
        ],
        [
            'label' => 'Arrays',
            'content' => $grids[1],
            'options' => ['id' => 'grid_array', 'data-alias_type' => $aliasTypes[1]],
        ],
        [
            'label' => 'Custom Generated',
            'content' => $grids[2],
            'options' => ['id' => 'grid_custom', 'data-alias_type' => $aliasTypes[2]],
        ],
        [
            'label' => 'Custom Multi',
            'content' =>$grids[3],
            'options' => ['id' => 'grid_custom_multi', 'data-alias_type' => $aliasTypes[3]],
        ],
        [
            'label' => 'List Entries',
            'content' => $grids[4],
            'options' => ['id' => 'grid_list_entry', 'data-alias_type' => $aliasTypes[4]],
        ],
        /* [
            'label' => 'Example',
            'url' => 'http://www.example.com',
        ],
        [
            'label' => 'Dropdown',
            'items' => [
                 [
                     'label' => 'DropdownA',
                     'content' => 'DropdownA, Anim pariatur cliche...',
                 ],
                 [
                     'label' => 'DropdownB',
                     'content' => 'DropdownB, Anim pariatur cliche...',
                 ],
                 [
                     'label' => 'External Link',
                     'url' => 'http://www.example.com',
                 ],
            ],
        ], */
    ],
]);
?>
<div class="button-block"><?= Html::a(Yii::t('app', 'Create'), Url::toRoute(['create']), ['class' => 'btn btn-primary pull-right']) ?></div>

<?php 
		$url = Url::current();
		$url = explode("?", $url);
		$url = $url[0];
		$url_arr = explode("/", $url);
		$url = str_replace($url_arr[count($url_arr)-1], "ajax", $url);
		$url_r = str_replace($url_arr[count($url_arr)-1], "requests", $url);
?>
<script>
$(document).ready(function(){
	var reqJson = [];
	<?php for($t=0; $t<count($aliasTypes); $t++){ ?>
		$('#w<?= $t ?>-filters').attr('id', 'cust<?= $t ?>-filters');
		$('#cust<?= $t ?>-filters').attr('t', '<?= $t ?>');
		$('#cust<?= $t ?>-filters [name="BaseSearch[AliasFormatType]"]').attr('name', 'AliasFormatType');

		$.ajax({
			  method: "POST",
			  url: "<?= $url ?>",
			  data: { AliasType: '<?= $aliasTypes[$t] ?>', AliasFormatType: $(this).val(), ajax: 'true', t: $('#cust<?= $t ?>-filters').attr('t'), <?php
					$kknum = 0; $kaf = GetAliasList::getFields();
					foreach($kaf as $kk => $kv){ 
						if($kk == 'AliasType' || $kk == 'AliasFormatType'){}
						else{
							echo $kk.':$("[name='. $kk .']").val()'.($kknum==(count($kaf)-1) ? '':', ');
						}
						$kknum++;
					}
				?> }
			})
			  .done(function( data ) {
				console.log("[data-alias_type='<?= $aliasTypes[$t] ?>']");
				$("[data-alias_type='<?= $aliasTypes[$t] ?>']").html(data);
				$('#w<?= $t ?>-filters').attr('id', 'cust<?= $t ?>-filters');
				$('#cust<?= $t ?>-filters').attr('t', '<?= $t ?>');
				$('#cust<?= $t ?>-filters [name="BaseSearch[AliasFormatType]"]').attr('name', 'AliasFormatType');
				//console.log( "Data Saved: " + msg );
			  });

		$('#cust<?= $t ?>-filters [name=AliasFormatType]').change( function(){
			$('#w<?= $t ?>-filters').attr('id', 'cust<?= $t ?>-filters');
			$('#cust<?= $t ?>-filters [name="BaseSearch[AliasFormatType]"]').attr('name', 'AliasFormatType');
			$.ajax({
			  method: "POST",
			  url: "<?= $url ?>",
			  data: { AliasType: '<?= $aliasTypes[$t] ?>', AliasFormatType: $(this).val(), ajax: 'true', t: $('#cust<?= $t ?>-filters').attr('t'), <?php
					$kknum = 0; $kaf = GetAliasList::getFields();
					foreach($kaf as $kk => $kv){ 
						if($kk == 'AliasType' || $kk == 'AliasFormatType'){}
						else{
							echo $kk.':$("[name='. $kk .']").val()'.($kknum==(count($kaf)-1) ? '':', ');
						}
						$kknum++;
					}
				?> }
			})
			  .done(function( data ) {
				console.log("[data-alias_type='<?= $aliasTypes[$t] ?>']");
				$("[data-alias_type='<?= $aliasTypes[$t] ?>']").html(data);
				$('#w<?= $t ?>-filters').attr('id', 'cust<?= $t ?>-filters');
				$('#cust<?= $t ?>-filters').attr('t', '<?= $t ?>');
				$('#cust<?= $t ?>-filters [name="BaseSearch[AliasFormatType]"]').attr('name', 'AliasFormatType');
				//console.log( "Data Saved: " + msg );
			  });

				reqJson[<?= $t ?>] = function(){ $.ajax({
					  method: "POST",
					  url: "<?= $url_r ?>",
					  data: { AliasType: '<?= $aliasTypes[$t] ?>', <?php
						$kknum = 0; $kaf = GetAliasList::getFields();
						foreach($kaf as $kk => $kv){ 
							if($kk == 'AliasType'){}
							else{
								echo $kk.':$("[name='. $kk .']").val()'.($kknum==(count($kaf)-1) ? '':', ');
							}
							$kknum++;
						}
					?> }
					})
					.done(function( data ) {
						return data;
					});
				};
				console.log(reqJson);
		} );

		$('#cust<?= $t ?>-filters [name="BaseSearch[AliasCode]"]').attr('name', 'AliasCode');
		<?php
		$knum = 0; $af = GetAliasList::getFields();
		foreach($af as $k => $v){ 
			if($k == 'AliasType' || $k == 'AliasFormatType'){}
			else{
				?>
				$('#cust<?= $t ?>-filters [name="BaseSearch[<?= $k ?>]"]').attr('name', '<?= $k ?>');
				<?php
			}
			$knum++;
		}
		?>

		$('#cust<?= $t ?>-filters [type=text]').on("keyup",  function(e){
			if(e.keyCode == 13){
				$.ajax({
				  method: "POST",
				  url: "<?= $url ?>",
				  data: { AliasType: '<?= $aliasTypes[$t] ?>', ajax: 'true', t: $('#cust<?= $t ?>-filters').attr('t'), <?php
					$kknum = 0; $kaf = GetAliasList::getFields();
					foreach($kaf as $kk => $kv){ 
						if($kk == 'AliasType'){}
						else{
							echo $kk.':$("[name='. $kk .']").val()'.($kknum==(count($kaf)-1) ? '':', ');
						}
						$kknum++;
					}
				?> }
				})
				  .done(function( data ) {
					console.log("[data-alias_type='<?= $aliasTypes[$t] ?>']");
					$("[data-alias_type='<?= $aliasTypes[$t] ?>']").html(data);
					$('#w<?= $t ?>-filters').attr('id', 'cust<?= $t ?>-filters');
					$('#cust<?= $t ?>-filters').attr('t', '<?= $t ?>');
					<?php
					$knum = 0; $af = GetAliasList::getFields();
					foreach($af as $k => $v){ 
						if($k == 'AliasType' || $k == 'AliasFormatType'){}
						else{
							?>
							$('#cust<?= $t ?>-filters [name="BaseSearch[<?= $k ?>]"]').attr('name', '<?= $k ?>');
							<?php
						}
						$knum++;
					}
					?>
					//console.log( "Data Saved: " + msg );
				  });

				reqJson[<?= $t ?>] = function(){ $.ajax({
					  method: "POST",
					  url: "<?= $url_r ?>",
					  data: { AliasType: '<?= $aliasTypes[$t] ?>', <?php
						$kknum = 0; $kaf = GetAliasList::getFields();
						foreach($kaf as $kk => $kv){ 
							if($kk == 'AliasType'){}
							else{
								echo $kk.':$("[name='. $kk .']").val()'.($kknum==(count($kaf)-1) ? '':', ');
							}
							$kknum++;
						}
					?> }
					})
					.done(function( data ) {
						return data;
					});
				};
				console.log(reqJson);
			}
		} );

		


		$('[t=<?= $t ?>].pagination > li > a').click(function(){
			if($(this).hasClass('disabled')){}
			else{
				var url = $(this).attr( "href");
				var tab = $('[t=<?= $t ?>].pagination').attr('t');
				console.log(url.replace('/ajax/ajax','/ajax'));
				$.ajax({
				  method: "GET",
				  url: url.replace('/ajax/ajax','/ajax'),
				  data: { AliasType: $('[t=<?= $t ?>].pagination').attr('aliastype'), ajax: 'true', t: tab, <?php
						$kknum = 0; $kaf = GetAliasList::getFields();
						foreach($kaf as $kk => $kv){ 
							if($kk == 'AliasType'){}
							else{
								echo $kk.':$("[name='. $kk .']").val()'.($kknum==(count($kaf)-1) ? '':', ');
							}
							$kknum++;
						}
					?> }
				})
				  .done(function( data ) {
					console.log("[data-alias_type='"+$('[t=<?= $t ?>].pagination').attr('aliastype')+"']");
					$("[data-alias_type='"+$('[t=<?= $t ?>].pagination').attr('aliastype')+"']").html(data);
					$('#w'+tab+'-filters').attr('id', 'cust'+$('[t=<?= $t ?>].pagination').attr('t')+'-filters');
					$('#cust'+tab+'-filters').attr('t', $('[t=<?= $t ?>].pagination').attr('t'));
					$('#cust'+tab+'-filters [name="BaseSearch[AliasFormatType]"]').attr('name', 'AliasFormatType');
					//console.log( "Data Saved: " + msg );
				  });
			}
			return false;
		});

		/*jQuery( function() {

    var availableTags = [*/
<?php

	/*$count = 1;
    foreach ( $results as $row ) 
    {
    	if($count>1) echo ",\n";
    	echo '{"label":"'.$row->display_name.'             '.$row->DatabaseTable.'", ';
    	echo '"value":"'.$row->ID.'"}';
     	$count++;
 	}*/
?>
/*];
    jQuery( "#tags" ).autocomplete({
      source: availableTags,

	    select: function (e, ui) {	       
			//console.log(ui.item.value);
			jQuery( "#tags" ).css("opacity", "0");

			?>?udn=' + ui.item.value;
	    },*/

/*	    change: function (e, ui) {
			//console.log(ui.item.value);
			jQuery( "#tags" ).css("opacity", "0");

			?>?udn=' + ui.item.value;
	    }*/
   /* });
  } );*/
<?php } //for $t ?>
});
</script>
<?php endif; ?>