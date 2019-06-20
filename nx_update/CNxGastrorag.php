<?
/**
* Обработка выгрузки Росхолода 
*
* @author Imageprom <raven@imageprom.com>
* @version 1.0
* @package NxUpdater
*/

namespace NxUpdater;

class CImportGastrorag extends CImportData {

	protected $deliverCode = 'gastrorag';
	
	protected static $source  = '/upload/nx_import/gastrorag.csv';
	protected static $archive = '/upload/nx_import/archive/gastrorag_##.csv';

	private static function getCurrency($cur) {

		switch ($cur) {
			case 'USD':
				return 'USD';
				break;
			
			case 'EUR':
				return 'EUR';
				break;

			case 'RUB':
				return 'RUB';
				break;

			default:
				return false;
				break;
		}

	}

	private static function getName($name) {
		$string = explode(PHP_EOL, $name);
		$name = mb_convert_encoding( $string[0], 'utf-8', 'windows-1251');
		return $name;
	}

	private static function formatPrice($price) {
		$price = str_replace(' ', '', $price);
		$price = str_replace(',', '.',$price);
		return floatval($price);
	}

	public static function getMargin($price) {

		if    ($price <= 100  ) $margin = 0.6;
		elseif($price <= 200  ) $margin = 0.55;
		elseif($price <= 250  ) $margin = 0.45;
		elseif($price <= 300  ) $margin = 0.3;
		elseif($price <= 400  ) $margin = 0.25;
		elseif($price <= 500  ) $margin = 0.2;
		elseif($price <= 700  ) $margin = 0.18;
		elseif($price <= 800  ) $margin = 0.16;
		elseif($price <= 2000 ) $margin = 0.15;
		else   					$margin = 0.1;

		return ($price + $price*$margin);
	}

	public function ReadSource() {
		
		try{
			
			$csvFile = fopen($_SERVER['DOCUMENT_ROOT'].self::$source, 'rt') or die('Ошибка!');

			while (( $csvData = fgetcsv($csvFile, 1000, ';')) !== FALSE) {
		
				$name = self::getName($csvData[2]);
				$code = $csvData[1];

				$curr = self::getCurrency($csvData[8]);

				if(!$curr) {
					CCatalogUpdater::Log('Item '.$code, 'Invalid Item');	
				}
				else {

					if(self::getCurrency($csvData[7])) {
						CCatalogUpdater::Log('Item '.$code, 'Invalid Item - invalid price');
					}	
					else {
						$price = self::formatPrice($csvData[7]);;
						$price = self::getMargin(floatval($price));

						$this->addItem(new CItem(strval($name), strval($code), $price, $curr));
					}
				} 
			}

			return true;
		}
		catch (Exception $e) {
    		echo $e->getMessage().PHP_EOL;
    		throw $e;
		} 
	}

	public static function ExistFile() {
		return file_exists($_SERVER['DOCUMENT_ROOT'].self::$source);
	}

	public function Archive() {

		$target = str_replace('##', date('d_m_Y-H_i_s'), self::$archive);

		if (copy($_SERVER['DOCUMENT_ROOT'].self::$source, $_SERVER['DOCUMENT_ROOT'].$target)) {
    		unlink($_SERVER['DOCUMENT_ROOT'].self::$source);
    		return true;
		}

		return false;
	}

	public function UpdateCodeByName($IB) {

		$updater = new \NxUpdater\CCatalogUpdater($IB);
		$data = $this->GetData();

		$arSelect = Array('IBLOCK_ID', 'ID', 'NAME', 'XML_ID');

		foreach ($data as $arValue) {

			$arFilter = Array('IBLOCK_ID' => $IB, 'NAME' => $arValue->NAME);

			$dbRes = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

			if($arFields = $dbRes->GetNext()) {

				$updater->UpdateCode($arFields['ID'],  $arValue->CODE);
				$updater->UpdateDeliver($arFields['ID'], $this->deliverCode);

				//print_r($arFields);
			}

		}
	}
}
?>