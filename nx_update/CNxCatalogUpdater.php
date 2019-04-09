<?
/**
* Импорт данных 
*
* @author Imageprom <raven@imageprom.com>
* @version 1.0
* @package NxUpdater
*/

namespace NxUpdater;

define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'].'/upload/log/update_' . date("Y_m_d") . '.log');

\CModule::IncludeModule('catalog');
\CModule::IncludeModule('iblock');

use  \Bitrix\Catalog\Model\Price;

/**
* Класс обновления данных в битриксе
* @package NxUpdater
*/

class CCatalogUpdater {

	private static $PriceType = 1;
	private static $DeliveryField = 'NX_DELIVER';
	private $IBlock;

	/**
	* Получить массив объектов импорта
	* @var int $Iblock - ID инфоблока
	*/

	public function __construct($IBlock) {		
		try{
	
			if($IBlock) {
				if(is_array($name) || is_object($name)) throw new Exception('Invalid argument $IBlock, need number');
				$this->IBlock = intval($IBlock);
			}
			else throw new Exception('Empty argument $IBlock');
		}

		catch (Exception $e) {
    		throw $e;
		} 

	}

	public static function Log($code, $mess, $type = 0) {
		$err_text = '';
		if(!$type) $err_text = 'Error! ';
		echo '<p>'.$err_text.$code.' : '.$mess.'</p>'.PHP_EOL;
		AddMessage2Log($err_text.'Item '.$code.' : '.$mess, 'updater', 0, false);
	}

	/**
	* Обновить ценц
	* @var int $ID - ID элемента
	* @var string $Currency - код валюты
	* @var float $Price - цена
	* @var bool $EmptyPrice - устанавливать нулевые цены
	* @return true или false
	*/

	public function UpdatePrice ($ID, $Currency = 'RUB', $Price, $EmptyPrice = false) {	
		try{

			if(!$ID) {
				throw new Exception('Invalid element ID');
			}

			if(!$Price && !$EmptyPrice) {
				throw new Exception('Invalid element Price');
			}

			$arFields = Array(
			    'PRODUCT_ID' => $ID,
			    'CATALOG_GROUP_ID' => self::$PriceType,
			    'PRICE' => $Price,
			    'CURRENCY' => $Currency,
			);

			$dbPrice = \CPrice::GetList(
				array(),
        		array(
                	'PRODUCT_ID' => $ID,
                	'CATALOG_GROUP_ID' => self::$PriceType,
            	)
   			 );

			if ($arPrice = $dbPrice->fetch()) {
				return \CPrice::Update($arPrice['ID'], $arFields);
			}
			else {
				return \CPrice::Add($arFields);
			}
		}

		catch (Exception $e) {
    		throw $e;
		} 
	}

	/**
	* Присвоить код поставщика
	* @var int $ID - ID элемента
	* @var string $Deliver - код поставщика
	* @return true или false
	*/

	public function UpdateDeliver ($ID, $Deliver) {	
		try{
			
			if(!$ID) {
				throw new Exception('Invalid element ID');
			}

			if(!$Deliver) {
				throw new Exception('Invalid element Deliver');
			}

			\CIBlockElement::SetPropertyValuesEx($ID, $this->IBlock, array(self::$DeliveryField => $Deliver));
			return true;
		}

		catch (Exception $e) {
    		throw $e;
		} 
	}

	/**
	* Присвоить внешний код
	* @var int $ID - ID элемента
	* @var string $Code - код поставщика
	* @return true или false
	*/

	public function UpdateCode($ID, $Code) {	
		try{
			
			if(!$ID) {
				throw new Exception('Invalid element ID');
			}

			if(!$Code) {
				throw new Exception('Invalid element Code');
			}

			$el = new \CIBlockElement; 
			$res = $el->Update($ID, array('XML_ID' => $Code));
			return true;
		}

		catch (Exception $e) {
    		throw $e;
		} 
	}
	
	/**
	* Обновить цены
	* @var IImportData $source - источник данных
	* @var bool $EmptyPrice - устанавливать нулевую цену
	*/

	public function ImportPrice(IImportData $source, $EmptyPrice = false) {
		
		$deliverCode = $source->GetDeliverCode();

		if($source->ReadSource()) {

			self::Log($deliverCode, 'Start', 1);
			
			$arSelect = Array('IBLOCK_ID', 'ID', 'NAME', 'XML_ID', 'PROPERTY_'.self::$DeliveryField);
			$arFilter = Array('IBLOCK_ID' => $this->IBlock, 'PROPERTY_'.self::$DeliveryField => $deliverCode);
			$dbRes = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
			
			while($arFields = $dbRes->GetNext()) {
				$code = mb_convert_encoding($arFields['XML_ID'], 'utf-8', 'cp1251');

				if($Item = $source->Search($code)) {
					if($Item->PRICE ||  $EmptyPrice) {
						if($res = $this->UpdatePrice($arFields['ID'], $Item->CUR, $Item->PRICE, $EmptyPrice)) {
							self::Log('Item '.$code, 'Price update', 1);
						}
						else {
							self::Log('Item '.$code, 'Not updtade');
						}
					}
					else {
						self::Log('Item '.$code, 'Zero price in no-zero price mode');
					}
				}
				else {
					self::Log($code, 'Not found');
				}
			}
		}

		else {
			self::Log($deliverCode, 'Empty data');	
		}
	}
}
?>