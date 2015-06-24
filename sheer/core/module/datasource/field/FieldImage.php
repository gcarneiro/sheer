<?php

namespace Sh;

class FieldImage extends \Sh\DataSourceField {
	
	protected $dataType 			= 'image';
	protected $renderType			= 'file';
	
	protected $picturesMap = array(
		'sheer' => 'sheer'
	);
	
	/*
	 * Informações customizadas para imagens
	 */
	protected $imageMode = null;
	
	protected $type = array(
		1 => 'image/jpeg',
		2 => 'image/png',
		3 => 'image/gif'
	);
	
	/**
	 * Método para formatar o dado primitivo para o formato Sheer
	 *
	 * O dado primitivo do image é o objeto do filePicture
	 * Preciso buscar o dados no fileDocument
	 * @param string uniqueId
	 * @return object[fileDocument]
	 */
	static public function formatPrimitiveDataToSheer ($data) {
		//buscando o dado no fileDocument
		$fileDocument = \Sh\ContentProviderManager::loadItem('filePicture/filePicture', $data);
		return $fileDocument;
	}
	
	
	/* (non-PHPdoc)
	 * @see \Sh\DataSourceField::setPicturesMapXml()
	 * 
	 * Método que irá processar o xml de pictureMap e adicionar ao field
	 */
	public function setPicturesMapXml ( \SimpleXMLElement $xmlPictureMap ) {
		
		//verificando existencia de mapas
		if( !$xmlPictureMap->map ) {
			return;
		}
		
		//inserindo os mapas no controle
		foreach ( $xmlPictureMap->map as $map ) {
			$idMap = (string) $map->attributes()->id;
			if( strlen($idMap) == 0 ) {
				continue;
			}
			$this->picturesMap[$idMap] = $idMap;
		}
		
	}
	
	public function getPicturesMap() {
		return $this->picturesMap;
	}
	
}