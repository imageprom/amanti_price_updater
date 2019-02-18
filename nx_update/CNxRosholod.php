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
	protected static $file = __DIR__.'/Ostatki.xml';

	public function ReadSource() {
		
		try{
			
			$xmlData = file_get_contents(self::$source);
			file_put_contents(self::$file, $xmlData);
			unset($xmlData);

			if(!is_readable(self::$file)) throw new Exception('Can\'t read '.self::$file);
			if (filesize (self::$file) == 0) return false;

			$xml = simplexml_load_file(self::$file);

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
}
?>