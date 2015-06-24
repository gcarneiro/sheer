<?php

namespace Sh;

class FieldDateTime extends DataSourceField {
	
	protected $dataType = 'datetime';
	protected $mask		= 'datetime';
	protected $validationType	= 'datetime';
	
	/* (non-PHPdoc)
	 * @see \Sh\DataSourceField::validateValueInput()
	*/
	public function validateValueInput ($data) {
		
		//capturando forma primitiva, se vier ok o dado esta ok
		$primitiveDate = $this->formatInputDataToPrimitive($data);
		return !!$primitiveDate;
	}
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 * @param string $data YYYY-DD-MM
	 * @return array [
	 * 		day
	 * 		month
	 * 		year
	 * 		weekday
	 * 		date
	 * 		hour
	 * 		minute
	 * 		second
	 * 		time
	 * 		datetime
	 * 		dayName
	 * 		dayNameAbbr
	 * 		monthName
	 * 		monthNameAbbr
	 * 		timestamp [u]
	 * ]
	 */
	static public function formatPrimitiveDataToSheer ($data) {
	
		//CRIANDO DATA e HORA
		$date = \DateTime::createFromFormat('Y-m-d H:i:s', $data);
		if( !$date ) {
			return null;
		}
		
		$response = self::getSheerObjectFromDateTime($date);
	
		return $response;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato Sheer
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToSheer ($data) {
		
		//CRIANDO DATA
		$date = self::getDateTimeFromPrimitive($data);
		if( !$date ) {
			return null;
		}
	
		$response = self::getSheerObjectFromDateTime($date);
	
		return $response;
	}
	
	/**
	 * Método para formatar o dado inputado para o formato primitivo
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToPrimitive ($data) {
	
		//CRIANDO DATA
		$date = self::getDateTimeFromPrimitive($data);
		if( !$date ) {
			return null;
		}
	
		return $date->format('Y-m-d H:i:s');
	}
	
	/**
	 * @param unknown $string
	 * @return \DateTime
	 */
	static public function getDateTimeFromPrimitive ($string) {
		//CRIANDO DATA
		//primeiro tenho que determinar o modelo de entrada do horário, se foi completo ou parcial
		$formato = null;
		switch ( strlen($string) ) {
			case 10:
				$formato = 'd/m/Y';
				break;
			case 13:
				$formato = 'd/m/Y H';
				break;
			case 16:
				$formato = 'd/m/Y H:i';
				break;
			case 19:
				$formato = 'd/m/Y H:i:s';
				break;
		}
		
		if( !$formato ) { return null; }
		
		$date = \DateTime::createFromFormat($formato, $string);
		if( !$date ) {
			return null;
		}
		return $date;
	}
	
	/**
	 * Método para formatar dados do Sheer em dados que são inputados
	 * @param string $data
	 */
	static public function formatSheerDataToInput ($data) {
		
		if( !isset($data['dateTime']) ) {
			return null;
		}
		
		return $data['dateTime']->format('d/m/Y H:i:s');
	}
	
	static public function isSheerFormat ( $data ) {
	
		if( is_array($data) && isset($data['dateTime']) ) {
			return true;
		}
		return false;
	
	}
	
	/**
	 * Método para transformar o dateTime no array preparado para o Sheer
	 *
	 * @param \DateTime $dateTime
	 * @return array
	 */
	static public function getSheerObjectFromDateTime (\DateTime $dateTime) {
	
		//CRIANDO OBJETO DE RESPOSTA
		$response = array();
		//dados padrões de data
		$response['day']			= $dateTime->format('d');
		$response['month']			= $dateTime->format('m');
		$response['year']			= $dateTime->format('Y');
		$response['weekday']		= $dateTime->format('N');
		$response['date']			= $dateTime->format('d/m/Y');
		//dados padrões de hora
		$response['hour']			= $dateTime->format('H');
		$response['minute']			= $dateTime->format('i');
		$response['second']			= $dateTime->format('s');
		$response['time']			= $dateTime->format('H:i:s');
		//dados completos de data e hora
		$response['datetime']		= $response['date'].' '.$response['time'];
	
		//buscando nomes
		$weekDayName 	= \Sh\Library::getNamesFromWeekDay($response['weekday']);
		$monthName 		= \Sh\Library::getNamesFromMonth($response['month']);
	
		//nomenclaturas
		$response['dayName']		= $weekDayName['dayName'];
		$response['dayNameAbbr']	= $weekDayName['dayNameAbbr'];
		$response['monthName']		= $monthName['monthName'];
		$response['monthNameAbbr']	= $monthName['monthNameAbbr'];
		//timestamp and format unix
		$response['timestamp']		= (integer) $dateTime->format('U');
		$response['dateTime']		= $dateTime;
	
		return $response;
	}
	
}