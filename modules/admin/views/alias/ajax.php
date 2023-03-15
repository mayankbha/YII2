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
 
//ob_start();

if(!isset($_SESSION['screenData']['sessionData']['sessionhandle']))
	echo json_decode(["status"=>"logged_out"]);

use yii\grid\GridView;
use yii\helpers\Url;
use app\models\GetAliasList;

/* echo "<pre>".print_r($_REQUEST)."</pre>";
exit; */
?>
<?php 
$session = Yii::$app->session;
$count_types = [];

$AliasType = $_REQUEST['AliasType'];
$t = $_REQUEST['t'];
$current_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$limit = 10;
$reqFields = (trim($_REQUEST["AliasFormatType"])!=""?[[$_REQUEST["AliasFormatType"]], [$AliasType]]:[[$AliasType]]);
$reqFieldCol = (trim($_REQUEST["AliasFormatType"])!=""?["AliasFormatType", "AliasType"]:["AliasType"]);

$kknum = 0; $kaf = GetAliasList::getFields();
//var_dump($kaf);
foreach($kaf as $kk => $kv){  
	if($kk != 'AliasType' && $kk != 'AliasFormatType'){
		if(isset($_REQUEST[$kk])){
			if(trim($_REQUEST[$kk])!=''){
				array_push($reqFields, [$_REQUEST[$kk]]);
				array_push($reqFieldCol, $kk);
			}
		}
	}
	$kknum++;
}
if ($session->has('count_types') == false){
	$getAllFormatTypes = GetAliasList::callAPI("GetAliasList", $AliasType, 1, 99999999, "AliasFormatType", $reqFieldCol);
	$jsonToArray = GetAliasList::jsonToArray($getAllFormatTypes); $formatRaw = [];
	for($j=0;$j<count($jsonToArray);$j++){
		$formatRaw[] = $jsonToArray[$j]["AliasFormatType"];
	}
	$count_types[$t] = array_count_values($formatRaw);
	if(empty($count_types[$t])) $count_types[$t] = $session->get('count_types')[$t];
}
else{ 
	$count_types[$t] = $session->get('count_types')[$t];
}
//echo print_r($count_types);exit;
$current_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$bracket = 99999999; //($current_page <= 10 ? 100 : $current_page * 10);
$json = GetAliasList::callAPI("SearchAlias", $reqFields, 1, $bracket, "AliasFormat", $reqFieldCol); //3.6s
$total_records_for_database_fields = count(GetAliasList::jsonToArray($json));
$page_total = round($total_records_for_database_fields / $limit);
//ECHO $total_records_for_database_fields;exit;
/*$total_records_for_database_fields = 3700;
$page_total = 370;*/
$pager = GetAliasList::customPager(($total_records_for_database_fields<$limit?1:$page_total), $limit, $_REQUEST, $t);
//var_dump($reqFields);var_dump($reqFieldCol);exit;
$json = GetAliasList::callAPI("SearchAlias", $reqFields, $current_page, $limit, "", $reqFieldCol);
$arrayDataProvider = GetAliasList::generateProvider($json);

$gridOptions = GetAliasList::generateGridArray($arrayDataProvider, $searchModel, $fullData, $count_types[$t], "", $AliasType);
$grid = GridView::widget(array_merge($gridOptions, ['options' => ['id' => 'w'.$t]])).$pager;

echo GetAliasList::minifyHTML($grid);
//echo "<pre>".print_r($count_types)."</pre>";

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
		$('#w<?= $t ?>-filters').attr('id', 'cust<?= $t ?>-filters');
		$('#cust<?= $t ?>-filters').attr('t', '<?= $t ?>');
		$('#cust<?= $t ?>-filters [name="BaseSearch[AliasFormatType]"]').attr('name', 'AliasFormatType');
		$('#cust<?= $t ?>-filters [name=AliasFormatType]').change( function(){
			$('#w<?= $t ?>-filters').attr('id', 'cust<?= $t ?>-filters');
			$('#cust<?= $t ?>-filters [name="BaseSearch[AliasFormatType]"]').attr('name', 'AliasFormatType');
			$.ajax({
			  method: "POST",
			  url: "<?= $url ?>",
			  data: { AliasType: '<?= $AliasType ?>', AliasFormatType: $(this).val(), ajax: 'true', t: $('#cust<?= $t ?>-filters').attr('t'), <?php
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
				console.log("[data-alias_type='<?= $AliasType ?>']");
				$("[data-alias_type='<?= $AliasType ?>']").html(data);
				$('#w<?= $t ?>-filters').attr('id', 'cust<?= $t ?>-filters');
				$('#cust<?= $t ?>-filters').attr('t', '<?= $t ?>');
				$('#cust<?= $t ?>-filters [name="BaseSearch[AliasFormatType]"]').attr('name', 'AliasFormatType');
				//console.log( "Data Saved: " + msg );
			  });

				reqJson[<?= $t ?>] = function(){ $.ajax({
					  method: "POST",
					  url: "<?= $url_r ?>",
					  data: { AliasType: '<?= $AliasType ?>', <?php
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
			    var t = $(this);
				$.ajax({
				  method: "POST",
				  url: "<?= $url ?>",
				  data: { AliasType: '<?= $AliasType ?>', ajax: 'true', t: $('#cust<?= $t ?>-filters').attr('t'), <?php
					$kknum = 0; $kaf = GetAliasList::getFields();
					foreach($kaf as $kk => $kv){ 
						if($kk == 'AliasType'){}
						else{
							echo $kk.':t.parents("tr").find("[name='. $kk .']").val()'.($kknum==(count($kaf)-1) ? '':', ');
						}
						$kknum++;
					}
				?> }
				})
				  .done(function( data ) {
					console.log("[data-alias_type='<?= $AliasType ?>']");
					$("[data-alias_type='<?= $AliasType ?>']").html(data);
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
					  data: { AliasType: '<?= $AliasType ?>', <?php
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

		<?php if(isset($_REQUEST['AliasFormatType']))
			echo "$('.tab-pane.active select>option[value=\"".$_REQUEST['AliasFormatType']."\"]').attr('selected', true);";

			if(isset($_REQUEST['AliasDatabaseTable']))
				echo "$('.tab-pane.active input[name=AliasDatabaseTable]').val('".$_REQUEST['AliasDatabaseTable']."');";

			if(isset($_REQUEST['AliasDatabaseField']))
				echo "$('.tab-pane.active input[name=AliasDatabaseField]').val('".$_REQUEST['AliasDatabaseField']."');";

			if(isset($_REQUEST['AliasCode']))
				echo "$('.tab-pane.active input[name=AliasCode]').val('".$_REQUEST['AliasCode']."');";
		?>

		$('[t=<?= $t ?>].pagination > li > a').click(function(){
			if($(this).hasClass('disabled')){}
			else{
				var url = "<?= $url."?page=" ?>"+$(this).attr('data-page');
				var tab = $('[t=<?= $t ?>].pagination').attr('t');
				console.log(url);
				$.ajax({
				  method: "GET",
				  url: url.replace('/index','/ajax'),
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
});
<?php //ob_end_flush(); ?>