<?

ignore_user_abort(1);
set_time_limit(0);
error_reporting (E_ERROR | E_PARSE | E_WARNING);
ini_set('display_errors', 'on');

$_SERVER["DOCUMENT_ROOT"] = '/mnt/data/www/amanti/vhost/public';
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("HELP_FILE", "settings/cache.php");

require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main;
use NxUpdater;

echo '<pre>';

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/nx_update/CNxImportData.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/nx_update/CNxRosholod.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/nx_update/CNxCatalogUpdater.php');


$importData = new \NxUpdater\CImportRosholod();
$updater = new \NxUpdater\CCatalogUpdater(17);

$updater->ImportPrice($importData);

//$updater->UpdatePrice(3885, 'RUB', 0.1);

// $arSelect = Array('IBLOCK_ID', 'ID', 'NAME', 'XML_ID', 'PROPERTY_BRAND');
// $arFilter = Array('IBLOCK_ID' => 17, 'XML_ID' => '00%');
// $dbRes = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

// while($arFields = $dbRes->GetNext()) {

// 	echo $arFields['XML_ID'].' - '.$arFields['NAME'].' - '.$arFields['PROPERTY_BRAND_VALUE'].'<br />';

// 	//CIBlockElement::SetPropertyValuesEx($arFields['ID'], 17, array('NX_DELIVER' => 'rosholod'));

// 	$updater->UpdateDeliver($arFields['ID'], $tmp->GetDeliverCode());

// }

echo '</pre>';
?>