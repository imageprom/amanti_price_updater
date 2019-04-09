<?
/**
* Обработка выгрузки Росхолода 
*
* @author Imageprom <raven@imageprom.com>
* @version 1.0
* @package NxUpdater
*/

namespace NxUpdater;

class CImportRosholod extends CImportData {

	protected $deliverCode = 'rosholod';
	
	protected static $source = 'https://rosholod.org/files/XML/Ostatki.xml';
	protected static $file = '/upload/nx_import/Ostatki.xml';
	protected static $archive = '/upload/nx_import/archive/Ostatki.xml';

	public function ReadSource() {
		
		try{
			
			$xmlData = file_get_contents(self::$source);
			file_put_contents($_SERVER['DOCUMENT_ROOT'].self::$file, $xmlData);
			unset($xmlData);

			if(!is_readable($_SERVER['DOCUMENT_ROOT'].self::$file)) throw new Exception('Can\'t read '.$_SERVER['DOCUMENT_ROOT'].self::$file);
			if (filesize ($_SERVER['DOCUMENT_ROOT'].self::$file) == 0) return false;

			$xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].self::$file);

			if(count($xml->shop->offers->ДетальнаяЗапись) == 0) return false;
			foreach ($xml->shop->offers->ДетальнаяЗапись as $arItem) {

				if($arItem->Код && $arItem->Наименование) {
					//$arItem->Валюта;

					$name =  mb_convert_encoding($arItem->Наименование, 'utf-8', mb_detect_encoding($arItem->Наименование));
					$code =  mb_convert_encoding($arItem->Код, 'utf-8', mb_detect_encoding($arItem->Код));

					$this->addItem(new CItem(strval($name), strval($code), floatval($arItem->Цена))); 
				} 
			}
			return true;
		}
		catch (Exception $e) {
    		echo $e->getMessage().PHP_EOL;
    		throw $e;
		} 
	}

	public function Archive() {
		if (copy($_SERVER['DOCUMENT_ROOT'].self::$file, $_SERVER['DOCUMENT_ROOT'].self::$archive)) {
    		unlink($_SERVER['DOCUMENT_ROOT'].self::$file);
    		return true;
		}

		return false;
	}
}
?>