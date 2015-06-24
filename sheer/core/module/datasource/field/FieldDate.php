<?php

namespace Sh;

class FieldDate extends DataSourceField {
	
	protected $dataType 		= 'date';
	protected $mask				= 'date';
	protected $validationType	= 'date';
	
	/* (non-PHPdoc)
	 * @see \Sh\DataSourceField::validateValueInput()
	 */
	public function validateValueInput ($data) {
		
		$date = \DateTime::createFromFormat('d/m/Y', $data);
		
		return !!$date;
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
	 * 		dayName
	 * 		dayNameAbbr
	 * 		monthName
	 * 		monthNameAbbr
	 * 		timestamp [u]
	 * ]
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		
		switch ( strlen($data) ) {
			case 7:
				$data = $data.'-01';
				break;
			case 10:
				break;
		}
		
		//criando data
		$date = \DateTime::createFromFormat('Y-m-d', $data);
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
		
		switch ( strlen($data) ) {
			case 7:
				$data = '01/'.$data;
				break;
			case 10:
				break;
		}
		
		//CRIANDO DATA
		$date = \DateTime::createFromFormat('d/m/Y', $data);
		if( !$date ) {
			return null;
		}
		
		return $date->format('Y-m-d');
	}
	
	/**
	 * Método para formatar o dado inputado para o formato Sheer
	 * @param string $data
	 * @return string
	 */
	static public function formatInputDataToSheer ($data) {
		
		switch ( strlen($data) ) {
			case 7:
				$data = '01/'.$data;
				break;
			case 10:
				break;
		}
		
		//CRIANDO DATA
		$date = \DateTime::createFromFormat('d/m/Y', $data);
		if( !$date ) {
			return null;
		}
		
		$response = self::getSheerObjectFromDateTime($date);
	
		return $response;
	}
	
	/**
	 * Método para formatar dados do Sheer em dados que são inputados
	 * @param string $data
	 */
	static public function formatSheerDataToInput ($data) {
		
		if( !isset($data['dateTime']) ) {
			return null;
		}
		
		return $data['dateTime']->format('d/m/Y');
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