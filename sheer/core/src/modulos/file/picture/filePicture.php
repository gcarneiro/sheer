<?php

namespace Sh\Modules\filePicture;

/**
 * Método que irá forcar o browser a fazer download do arquivo
 * 
 * @param integer
 */
function download( ) {
	
	try {
		//VERIFICANDO ENVIO DAS HEADERS
		if( headers_sent() ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Impossível efetuar download do arquivo pois as headers HTTP já foram enviadas.'
			));
		}
		
		//CAPTURANDO IDENTIFICADORES DO ARQUIVO
		$idFilePicture = null;
		$imagePictureMap = null;
		$imageName = null;
		if( isset($_GET['i']) && $_GET['i'] ) {
			$idFilePicture = $_GET['i'];
		}
		if( isset($_GET['im']) && $_GET['im'] ) {
			$imagePictureMap = $_GET['im'];
		}
		if( isset($_GET['in']) && $_GET['in'] ) {
			$imageName = $_GET['in'];
		}
		
		//VERIFICANDO INTEGRIDADE DO ARQUIVO
		if( !$idFilePicture || !$imagePictureMap || !$imageName ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Arquivo inválido para download.'
			));
		}
		
		//CAPTURANDO ARQUIVO
		$file = \Sh\ContentProviderManager::loadItem('filePicture/filePicture', $idFilePicture);
		if( !$file ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Arquivo inválido para download.'
			));
		}
		$imagem = $file['pictures'][$imagePictureMap][$imageName];
		
		//MARCANDO DOWNLOAD DO ARQUIVO
		$fileUpdate = array(
			'id' => $file['id'],
			'downloads' => ++$file['downloads']
		);
		$response = \Sh\ContentActionManager::doAction('filePicture/filePicture_update', $fileUpdate);
		
		//DETERMINANDO INFORMAÇÕES PARA DOWNLOAD DO ARQUIVO
		$downloadName = $file['nameFull'];
		
		
		//ENVIANDO ARQUIVO
		
		//CASO O PARAMETRO FDO (FORCE DOWNLOAD) ESTEJA SETADO COMO UM , FORCAMOS O DOWNLOAD DA IMAGEM
		if( isset($_GET['fdo']) && $_GET['fdo']=='1' ) {
			header('Content-Disposition: attachment; filename="'.$downloadName.'"');
		}
		
		// Configuramos os headers que serão enviados para o browser
		//header('Content-Description: File Transfer');
		header('Content-Type: '.$file['mimeType']);
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . $imagem['size']);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Expires: 0');
		readfile($imagem['path']);
	}
	catch (\Sh\SheerException $e) {
		header('Content-Type: text/html; charset=UTF-8');
		echo '<h1>'.$e->getErrorMessage().'</h1>';
	}
	
	exit;
	
}

/**
 * @author Guilherme
 * 
 * DataProvider de detalhes customizado.
 *
 * @return null se não encontrar arquivo
 * 			array caso o encontre
 *
 */
class filePictureParser extends \Sh\GenericDataParser {
	
	public function parseData($data) {
		
		if( $data ) {
			foreach ( $data as $idContent=>&$content ) {
				
				
				//capturando pictureMap
				$picsMap = json_decode($content['picsMap'], true);
				//removendo pictureMap
				$content['picsMap'] = null;
				unset($content['picsMap']);
				//processando downloadLink
				if( $picsMap ) {
					foreach ( $picsMap as $idMap=>&$map ) {
						foreach ( $map as $name=>&$image ) {
							$image['downloadLink'] = 'dfp.php?i='.$content['id'].'&im='.$idMap.'&in='.$name;
						}
					}
				}
				$content['pictures'] = $picsMap;
			}
		}
		
		return $data;
		
	}
	
}

/**
 * @author Guilherme
 * 
 * Customizo este ActionHandler pois ele irá receber exatamente as informações do $_FILES do php e deverá adicionar isso no Sheer e devolver um objeto de controle para este arquivo
 *
 */
class filePicture_add extends \Sh\GenericAction {
	
	
	public function doAction($data) {
		
		$image = $data['image'];
		
		//TRATANDO ERROS DE SUBMISSAO
		$errorMessage = null;
		switch( $image['error'] ) {
			case UPLOAD_ERR_INI_SIZE:
				$errorMessage = 'O arquivo excede o tamanho máximo de arquivos permitido.';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$errorMessage = 'O arquivo excede o tamanho máximo de arquivos permitido.';
				break;
			case UPLOAD_ERR_PARTIAL:
				$errorMessage = 'O arquivo foi apenas parcialmente enviado.';
				break;
			case UPLOAD_ERR_NO_FILE:
				$errorMessage = 'Nenhum arquivo foi enviado.';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$errorMessage = 'Não foram encontrados pastas temporárias para gravação do arquivo.';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$errorMessage = 'Erro ao tentar gravar no disco.';
				break;
			case UPLOAD_ERR_EXTENSION:
				$errorMessage = 'Problemas internos de extensões.';
				break;
		}
		if( $errorMessage ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => $errorMessage
			));
		}
		
		//DETERMINANDO MiMETYPE
		$mimeType = image_type_to_mime_type(exif_imagetype($image['tmp_name']));
		$mimeExt = \Sh\ImageLibrary::$mimeToExt[$mimeType];
		
		//CRIANDO ESTRUTURA E DADOS DO FILEPICTURE
		$filePicture = array(
			'id'				=> \Sh\Library::getUniqueId(true),
			'name'				=> null,
			'nameFull'			=> $image['name'],
			'nameExt'			=> null,
			'legenda'			=> null,
			'adicionadoEm'		=> date('d/m/Y H:i:s'),
			'adicionadoPor'		=> \Sh\AuthenticationControl::getAuthenticatedUserInfo('id'),
			'atualizadoEm'		=> date('d/m/Y H:i:s'),
			'atualizadoPor'		=> \Sh\AuthenticationControl::getAuthenticatedUserInfo('id'),
			'downloads'			=> 0,
			'mimeType'			=> $mimeType,
			'remove'			=> 2,
			'picsMap'			=> null
		);
		
		//DEFININDO INFORMAÇÕES DO NOME, EXTENSÃO E PATH FINAL
		//determinando extensao
		$filePicture['nameExt'] = $mimeExt;
		$positionBarra = strrpos($filePicture['nameFull'], '/');
		$positionBarra2 = strrpos($filePicture['nameFull'], '\\');
		//capturando nome do arquivo
		if( $positionBarra !== false ) {
			$filePicture['name'] = substr($filePicture['nameFull'], $positionBarra);
		}
		else if( $positionBarra2 !== false  ) {
			$filePicture['name'] = substr($filePicture['nameFull'], $positionBarra2);
		}
		else {
			$filePicture['name'] = $filePicture['nameFull'];
		}
		
		//determinando filepath
		$filePicture['path'] = SH_PUBLIC_DATA_FILES.$filePicture['id'];
		if( $filePicture['nameExt'] ) {
			$filePicture['path'] .= '.'.$filePicture['nameExt'];
		}
		
		//GERANDO ARQUIVOS DE IMAGENS E GERANDO O FILES-MAPPER COM JSON
		$fileMapper = \Sh\ImageLibrary::generatePicturesFromMap($filePicture, $image['tmp_name'], $data['maps']);
		$filePicture['picsMap'] = json_encode($fileMapper);
		
		//SALVANDO DADOS DO ARQUIVO
		$response = \Sh\ContentActionManager::doPrimitiveAction('filePicture/filePicture', 'add', $filePicture, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//reinserindo fileMapper para retorno
		$response['data']['picsMap'] = $fileMapper;
		
		return $response;
		
		
	}
	
	
}

/*
 * ActionHandler para inserção de imagens de forma direta
*/
class adicionarImagemDireta extends \Sh\GenericAction {

	public function doAction($data) {

		//INJETANDO O MAP DO SHEER
		$data['maps'] = ['sheer'=>'sheer'];

		return \Sh\ContentActionManager::doAction('filePicture/filePicture_add', $data, $this->connection);

	}

}


