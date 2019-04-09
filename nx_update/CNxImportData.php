<?
/**
* Базовые классы для работы с источниками импорта 
*
* @author Imageprom <raven@imageprom.com>
* @version 1.0
* @package NxUpdater
*/

namespace NxUpdater;

/**
* Исключения
* @package NxUpdater
*/

class Exception extends \Exception{}

/**
* Структурный класс единицы импорта
* @package NxUpdater
*/


class CItem {
	
	private $arData = array('NAME' => '', 'CODE' => '', 'PRICE' => 0, 'CUR' => 'RUB');

	public function __construct($name = '', $code = '', $price = 0, $cur = 'RUB') {	
		
		try{
	
			if($name) {
				if(is_array($name) || is_object($name)) throw new Exception('Invalid argument $name, need string');
				$this->arData['NAME'] = strval($name);
			}

			if($code) {
				if(is_array($code) || is_object($code)) throw new Exception('Invalid argument $code, need string');
				$this->arData['CODE'] = strval($code);
			}

			if($cur) {
				if(is_array($cur) || is_object($cur))  throw new Exception('Invalid argument $cur, need string');
				$this->arData['CUR']  = strval($cur);
			}	
			if($price) {
				if(is_array($cur) || is_object($cur))  throw new Exception('Invalid argument $cur, need number');
				$this->arData['PRICE']  = floatval($price);
			}
		}

		catch (Exception $e) {
    		throw $e;
		} 

	}
	
	public function __set($name, $value) {
		
		try{
			switch ($name) {
				case 'NAME':
				case 'CODE':
				case 'CUR':
					$this->arData[$name] = strval($value);
					break;
				case 'PRICE':
					$this->arData[$name] = floatval($value);
					break;
				default:
					throw new Exception('Undefined field '.$name);
					break;
			}
		}

		catch (Exception $e) {
    		throw $e;
		} 
    }

    public function __get($name) {

        try{
			if (array_key_exists($name, $this->arData)) {
            	return $this->arData[$name];
        	}
			else {		
				throw new Exception('Undefined field '.$name);
			}
		}

		catch (Exception $e) {
    		throw $e;
		} 
	}
}

/**
* Интерфейс импотируемой сущности
* @package NxUpdater
*/

interface IImportData {
	/**
	* Инициализая и чтение источника данных
	* @return void
	*/
	public function ReadSource();

	/**
	*Получить массив объектов импорта
	* @return CItem или false
	*/
	public function GetData();

	/**
	*Получить массив объектов импорта
	* @return CItem или false
	*/
	public function GetDeliverCode();

	/**
	* Колличество объектов импорта
	* @return int
	*/
	public function CountItems();

	/**
	* Поиск по коду элемента
	* @return CItem[] или false
	*/
	public function Search($code);

	/**
	* Сохранение данных в архив
	* @return void
	*/
	
	public function Archive();
}

/**
* Базовый  абстрактный класс импотируемой сущности
* @package NxUpdater
*/

abstract class CImportData implements IImportData {

	/**
	* Данные для обновления
	* @var CItem[]
	*/

	private $arData = array();
	protected $deliverCode = false;

	abstract public function ReadSource();

	public function CountItems() {
		return count($this->arData);
	}

	public function GetDeliverCode() {
		return $this->deliverCode;
	}

	public function GetData() {
		if($this->CountItems() > 0) return $this->arData;
		else return false;
	}

	public function Search($code) {
		try{
			if(!$code) throw new Exception('Empty search $code');
			if(is_array($code) || is_object($code)) throw new Exception('Invalid argument $code, need string');

			if($this->CountItems() > 0) {
				foreach ($this->arData as $arItem) {
					if($arItem->CODE == $code) return $arItem;
				}
			}
			return false;
		}
		catch (Exception $e) {
    		throw $e;
		} 
	}

	protected function addItem(CItem $item) {
		$this->arData[] = $item;
	}    	
}
?>