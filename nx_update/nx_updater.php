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
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/nx_update/CNxGastrorag.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/nx_update/CNxCatalogUpdater.php');


$importData = new \NxUpdater\CImportRosholod();
$updater = new \NxUpdater\CCatalogUpdater(17);
$updater->ImportPrice($importData);
$importData->Archive();

unset($updater);

$updater = new \NxUpdater\CCatalogUpdater(17);
$importData = new \NxUpdater\CImportGastrorag();
$updater->ImportPrice($importData);
$importData->Archive();

exec('chmod -R 775 '.$_SERVER["DOCUMENT_ROOT"].'/upload/nx_import/');
exec('chown -R amanti:www-data '.$_SERVER["DOCUMENT_ROOT"].'/upload/nx_import/');

echo '</pre>';
?>