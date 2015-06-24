<?php

namespace Sh;

abstract class ImageLibrary {
	
	static protected $initialized = false;
	static protected $imagesMapSystem = array();
	static protected $imagesMapProject = array();
	
	/**
	 * @var array
	 */
	static public $mimeToExt = array(
		'image/gif'			=> 'gif',
		'image/jpg'			=> 'jpg',
		'image/jpeg'		=> 'jpg',
		'image/png'			=> 'png',
		'image/psd'			=> 'psd',
		'image/bmp'			=> 'bmp',
		'image/tiff'		=> 'tiff'
	);
	
	static public $extToMime = array(
		'gif'			=> 'image/gif',
		'jpeg'			=> 'image/jpg',
		'png'			=> 'image/png',
		'psd'			=> 'image/psd',
		'bmp'			=> 'image/bmp',
		'tiff'			=> 'image/tiff'
	);
	
	static public function init() {
		
		//controle de inicializacao
		if( self::$initialized ) {
			return;
		}
		self::$initialized = true;
		
		//PROCESSANDO IMAGESMAP DO SHEER
		self::$imagesMapSystem = json_decode(file_get_contents( SH_SETUP_PATH.'pictureMap.json' ), true);
		if( !self::$imagesMapSystem ) {
			throw new \Sh\FatalErrorException(array(
				'code' => null,
				'message' => 'Erro ao carregar configurações pictureMap do sistema.'
			));
		}
		self::processPictureMap(self::$imagesMapSystem);
		
		//PROCESSANDO IMAGESMAP DO PROJETO
		$filePath = SH_PROJECT_SETUP_PATH.'pictureMap.json';
		if( is_file($filePath) ) {
			$jsonString = file_get_contents( $filePath );
			if( strlen($jsonString) > 2 ) {
				$array = json_decode($jsonString, true);
				if( is_array($array) ) {
					self::$imagesMapProject = $array;
				}
			}
		}
		self::processPictureMap(self::$imagesMapProject);
		
	}
	
	/**
	 * Método para processar e completar dados faltantes no pictureMap
	 * Utilizado via referencia
	 * 
	 * @param unknown $pictureMap
	 */
	static protected function processPictureMap ( &$pictureMap ) {
		
		if( $pictureMap ) {
			foreach ( $pictureMap as &$pictureType ) {
				if( $pictureType ) {
					foreach ( $pictureType as &$pictureName ) {
						
						if( !isset($pictureName['width']) ) {
							$pictureName['width'] = 0;
						}
						else if ( !isset($pictureName['height']) ) {
							$pictureName['height'] = 0;
						}
						if( !isset($pictureName['processor']) ) {
							$pictureName['processor'] = null;
						}
						
					}
				}
			}
		}
		
		
	}
	
	
	
	/**
	 * Método para processar imagem e gerar os arquivos a partir de um picturesMap
	 * 
	 * @param string $filePicture Conteúdo filePicture de controle que é responsável pela geracao
	 * @param string $filePath endereço do arquivo de imagem enviado
	 * @param array $maps => array[idMaps]
	 * 
	 * @return array (
	 * 		idMap => array(
	 * 			name = > array(
	 * 				'id' => $name,
	 *				'width' => null,
	 *				'height' => null,
	 *				'size' => null,
	 *				'name' => $imageName,
	 *				'path' => SH_PUBLIC_DATA_PICTURES.$imageName
	 * 			)
	 * 		)
	 * )
	 */
	static public function generatePicturesFromMap ( $filePicture, $filePath, $maps ) {
		
		try {
			
			//CRIANDO IMAGICK
			$imageResource = new \Imagick($filePath);
			$imageResourceGeometry = $imageResource->getImageGeometry();
			//controle de arquivos gerados
			$fileMapper = array();
			//capturando configuracoes do projeto FIXME so utilizado devido ao bug do RELATIVE PATH do Imagick
			$projectPath = \Sh\ProjectConfig::getProjectConfiguration();
			
			//BUSCANDO MAPS
			foreach ( $maps as $idMap ) {
				
				//BUSCANDO O PICTUREMAP
				$pictureMap = self::getPictureMapById($idMap);
				if( !$pictureMap ) { continue; }
					
				//iniciando mapa no fileMapper
				$fileMapper[$idMap] = array();
					
				//ITERANDO PELAS IMAGENS DO MAP
				foreach ( $pictureMap as $name=>$nameMap ) {
					
					$imageName = $filePicture['id'].'-'.$idMap.'-'.$name;
					
					//DETERMINANDO PROPRIEDADES DA IMAGEM
					$tmpMapper = array(
							'id' => $name,
							'width' => $imageResourceGeometry['width'],
							'height' => $imageResourceGeometry['height'],
							'size' => null,
							'name' => $imageName,
							'path' => SH_PUBLIC_DATA_PICTURES.$imageName,
							'realPath' => realpath(SH_PUBLIC_DATA_PICTURES).'/'.$imageName
					);
					
					//CLONANDO IMAGEM ORIGINAL
					$tmpImage = clone $imageResource;
					
					//VERIFICO O PROCESSOR PARA DETERMINAR SE DEVEMOS UTILIZA-LO EM VER DO PROCESSOR PADRAO DO SHEER
					//Tendo imageProcessor próprio
					if( $nameMap['processor'] ) {
						//Verifico que herda do GenericImageProcessor
						if( is_subclass_of($nameMap['processor'], '\\Sh\\GenericImageProcessor') ) {
							//Chamo a função customizada
							$imageProcessor = new $nameMap['processor']($tmpImage, $tmpMapper, $nameMap);
							$tmpImage = $imageProcessor->execute();
						}
						//Não tendo herança interrompo toda a execução
						else {
							throw new \Sh\FatalErrorException(array(
								'code' => null,
								'message' => 'A Classe "'.$nameMap['processor'].'" não herda de "\\Sh\\GenericImageProcessor" e não pode ser utilizada'
							));
						}
					}
					//Não possuindo imageProcessor proprio utilizo o do sheer
					else {
						//Chamo a função default do sheer
						$imageProcessor = new \Sh\ImageProcessor($tmpImage, $tmpMapper, $nameMap);
						$tmpImage = $imageProcessor->execute();
					}
					
					//Recalculando dimensoes da imagem após transformação
					$geometry = $tmpImage->getImageGeometry();
					$tmpMapper['width'] = $geometry['width'];
					$tmpMapper['height'] = $geometry['height'];
					
					//determinando extensão após transformação
					$nameExt = self::getExtensionFromImagickFormat($tmpImage->getimageformat());
					if( $nameExt ) {
						$tmpMapper['path'] = $tmpMapper['path'].'.'.$nameExt;
						$tmpMapper['realPath'] = $tmpMapper['realPath'].'.'.$nameExt;
					}
					
					//Salvando a imagem em disco
					$saved = $tmpImage->writeimage($tmpMapper['realPath']);
					if( !$saved ) {
						throw new \Sh\SheerException(array(
							'code' => null,
							'message' => 'Erro ao realizar gravação do arquivo de imagem em disco'
						));
					}
					
					//recarregando arquivo para obter o tamanho
					$tmpMapper['size'] = filesize($tmpMapper['realPath']);
					
					//inserindo no mapper final
					$fileMapper[$idMap][$name] = $tmpMapper;
					
				}
				
			}
			
			return $fileMapper;
			
		}
		catch ( \ImagickException $e ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'ImageMagick Exception '.$e->getMessage()
			));
		}
		
	}
	
	/**
	 * Método para retornar o pictureMap pelo seu id
	 * @param unknown $idMap
	 * @return multitype:|NULL
	 */
	static protected function getPictureMapById( $idMap ) {
		
		//buscando no sheer
		if( isset(self::$imagesMapSystem[$idMap]) ) {
			return self::$imagesMapSystem[$idMap];
		}
		else if ( isset(self::$imagesMapProject[$idMap]) ) {
			return self::$imagesMapProject[$idMap];
		}
		else {
			return null;
		}
		
	}
	
	/**
	 * Converte o formato retornado pelo IMagick e devolve a extensão para o arquivo
	 * 
	 * @param string $format
	 * @return string
	 */
	static public function getExtensionFromImagickFormat( $format ) {
		
		if( isset(self::$extToMime[strtolower($format)]) ) {
			$x = self::$extToMime[strtolower($format)];
			$x = self::$mimeToExt[$x];
			return $x;
		}
		else {
			return null;
		}
		
	}
	
	
	
	
}


/*
abstract class ImageLibrary2 {
	
	static protected $initialized 	= false;
	static protected $imageModes 	= array();
	static protected $shImageModes 	= array();
	
	/**
	 * Método inicializador dos modos de imagens do Sistema e Projeto
	 * 
	 * @return boolean
	static public function init() {
		
		if( self::$initialized ) {
			return false;
		}
		self::$initialized = true;
		
		self::processProjectImageModes();
		self::processSheerImageModes();
		
	}
	
	/**
	 * Método para recuperar os modos desejados junto com os do Sheer
	 * 
	 * @param array $modes
	 * @return array
	static public function getModes( $modes ) {
		
		//processando input
		if( !is_array($modes) ) {
			if( !$modes ) {
				return null;
			}
			$modes = array($modes);
		}
		
		//adicionando modos do sheer
		$fullModes = self::$shImageModes;
		
		//adicionando modos escolhidos
		foreach ( $modes as $modeName ) {
			if( isset(self::$imageModes[$modeName]) ) {
				$fullModes[$modeName] = self::$imageModes[$modeName];
			}
		}
		
		return $fullModes;
		
	}
	
	/**
	 * Método que irá ler e processar todas as configurações de imagens do sheer
	 * 
	 * @throws \Sh\FatalErrorException
	static protected function processSheerImageModes () {
		
		$filepath = SH_SETUP_PATH.'imageModes.json';
		self::processImageModesFromFile($filepath, true);
		
	}
	
	/**
	 * Método que irá ler e processar todas as configurações de imagens do projeto
	 * 
	 * @throws \Sh\FatalErrorException
	static protected function processProjectImageModes () {
		
		$filepath = SH_PROJECT_SETUP_PATH.'imageModes.json';
		self::processImageModesFromFile($filepath);
		
	}
	
	static protected function processImageModesFromFile ($filepath, $sheerModes=false) {
		$content = file_get_contents($filepath);
		$config = json_decode(utf8_encode($content), true);
		
		if( $config === null ) {
			
			$jsonError = json_last_error();
			
			throw new \Sh\FatalErrorException(array(
				'code' => null,
				'message' => 'Erro ao processar arquivo JSON de configuração de modo de imagens. Erro: "'.$jsonError.'"'
			));
		}
		
		if($sheerModes) {
			self::$shImageModes = $config;
		}
		
		self::$imageModes = array_merge(self::$imageModes, $config);
	}
	
}
*/