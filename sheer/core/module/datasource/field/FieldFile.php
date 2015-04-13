<?php

namespace Sh;

class FieldFile extends DataSourceField {
	
	protected $dataType 			= 'file';
	protected $renderType			= 'file';
	
	protected static $filePath = './data/files/';
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 * 
	 * O dado primitivo do file é o objeto do fileDocument
	 * Preciso buscar o dados no fileDocument
	 * @param string uniqueId
	 * @return object[fileDocument]
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		//buscando o dado no fileDocument
		$fileDocument = \Sh\ContentProviderManager::loadItem('fileDocument/fileDocument', $data);
		return $fileDocument;
	}
	
	/**
	 * Método para verificar se a estrutura do array de input está correta contendo todos os dados que o PHP envia
	 *
	 * @return boolean
	 */
	static protected function checkInputData ($data) {
		
	
		if(
			!(
				isset($data['name'])
				&& isset($data['type'])
				&& isset($data['tmp_name'])
				&& isset($data['error'])
				&& isset($data['size'])
			)
		) {
			return false;
		}
	
		return true;
	
	}
	
}