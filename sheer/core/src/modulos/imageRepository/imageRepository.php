<?php

namespace Sh\Modules\imageRepository;

/**
 * @author Guilherme
 *
 * Este dataProvider vai funcionar diferente no seu retorno.
 * Qualquer customização geral sobre as informações do album devem ser realizadas no dataParser do album_detalhes
 * Ele irá retornar:
		objeto:imageRepository em caso de sucesso
		false em caso do dataSource não permitir um imageRepository
		null em alguma outra falha
 */
class getAlbum extends \Sh\GenericContentProvider {
	
	public function getData( $filters=array(), $configs=array() ) {
		
		$imageRepository = null;
		
		//verificando parametros
		if( !isset($filters['idContent']) || !isset($filters['idDataSource']) ) {
			return null;
		}
		
		//Tento diretamente criar o album
		$album = \Sh\ContentActionManager::doAction('imageRepository/album_add', $filters);
		//em caso de obter o IR
		if( $album['status'] ) {
			$imageRepository = \Sh\ContentProviderManager::loadContentById('imageRepository/album', $album['data']['id']);
		}
		else {
			if( $album['code'] == 'IR000' ) {
				$imageRepository = false;
			}
			else {
				$imageRepository = null;
			}
		}
		
		return $imageRepository;
	}
	
}

/**
 * @author Guilherme
 *
 * Não extendo o Generic para poder utilizar toda a inteligencia do padrao e somente fazer uns tratamentos diferentes posterior ao carregamento do conteudo
 */
class albumDetalhes extends \Sh\ContentProvider {
	
	public function getData( $filters=array(), $configs=array() ) {
		
		$data = parent::getData($filters, $configs);
		if( $data['total'] != 1 ) {
			return [
				'available' => 0,
				'total' => 0,
				'results' => null
			];
		}
		
		//capturando o album
		$album = reset($data['results']);
		//marcando a visualizacao do album
		self::novaVisualizacaoAlbum($album['id']);
		//ordenando suas fotos
		//ORDENAR AS FOTOS DO ALBUM
		$imagensOrdenadas = array();
		if( $album['imagens'] ) {
				
			//Operando por todas as imagens e organizando em referencia a lista encadeada
			$idProximo = null;
			do {
				//verificando se é a primeira operacao, se for o idProximo = primeiro
				if( $idProximo === null ) {
					reset($album['imagens']);
					$idProximo = key($album['imagens']);
				}
		
				//buscando agora a imagem para as operacoes
				$imagem = &$album['imagens'][$idProximo];
				unset($album['imagens'][$imagem['id']]);
				//reposicionando o pictures do primeiro
				$imagem['pictures'] = &$imagem['idPicture']['pictures'];
				unset($imagem['idPicture']['pictures']);
				//colocando no array ordenado
				$imagensOrdenadas[$imagem['id']] = $imagem;
				//pegando o idProximo
				$idProximo = $imagem['idProximo'];
		
			} while ( $idProximo != null );
				
			//FIXME verifico se sobrou alguma imagem = idProximo sofreu problema em uma insercao multipla
			if( $album['imagens'] ) {
				foreach ( $album['imagens'] as $idImagem=>&$imagem ) {
					
					//reposicionando o pictures do primeiro
					$imagem['pictures'] = &$imagem['idPicture']['pictures'];
					unset($imagem['idPicture']['pictures']);
					//colocando no array ordenado
					$imagensOrdenadas[$imagem['id']] = $imagem;
					unset($album['imagens'][$idImagem]);
				}
			}
				
			$album['imagens'] = &$imagensOrdenadas;
		}
		//recoloco este novo array no retorno e retorno
		$data['results'][$album['id']] = &$album;
		return $data;
	}
	
	/**
	 * Método para registrar uma nova visualização para a combinação repositorio+profileAcesso
	 * @param string $idRepository
	 * @return boolean
	 */
	static protected function novaVisualizacaoAlbum ($idRepository) {
	
		//gerando controle
		$user = \Sh\AuthenticationControl::getAuthenticatedUserInfo();
		$albumView = array(
				'idRepository' => $idRepository,
				'idProfile' => $user['profile']['id']
		);
	
		//REGISTRANDO MAIS UMA VISUALIZACAO
		//buscando o registro já existente
		$irViewed = \Sh\ContentProviderManager::loadContent('imageRepository/viewed_lista', $albumView);
		//se temos já o registro atualizamos
		if( $irViewed['total'] > 0 ) {
			$irViewed = reset($irViewed['results']);
			//atualizarei esse registro na mão para não termos problema de soma
			$sqlQuery = 'UPDATE sh_imageRepositoryViewed SET times=times+1 WHERE id="'.$irViewed['id'].'"';
			$connection = \Sh\DatabaseConnectionProvider::newDatabaseConnection();
			$response = $connection->exec($sqlQuery);
			if( $response ) {
				$connection->commit();
			}
		}
		//nao encontrando preciso cadastrar
		else {
			$albumView['id'] = \Sh\Library::getUniqueId();
			$albumView['times'] = 1;
			//não faço aqui a verificação de sucesso pois esse passo é um adicional. Se der problema não quero interromper o fluxo padrão
			$response = \Sh\ContentActionManager::doAction('imageRepository/viewed_add', $albumView);
		}
	
		return true;
	}
	
}

/**
 * @author Guilherme
 * ContentProvider para buscar todas as informações de um ImageRepository específico
 * Utilizado no gerencimento das fotos pelo sheer
 * 
 */
class gerenciarIR extends \Sh\GenericContentProvider {
	
	public function getData( $filters=array(), $configs=array() ) {
		
		//CAPTURAR O IMAGE.REPOSITORY
		$imageRepository = \Sh\ContentProviderManager::loadContent('imageRepository/getAlbum', $filters, $configs);

		//caso tenha ocorrido algum erro ao recuperar o imageRepository
		if( $imageRepository === null ) {
			return [
				'available' => 0,
				'total' => 0,
				'results' => [
					'imageRepository' => null,
					'content' => null
				]
			];
		}
		
		//CARREGAR O CONTEUDO
		$conteudo = \Sh\ContentProviderManager::loadContentById($imageRepository['idDataSource'], $imageRepository['idContent']);
		//caso não tenha encontrado o conteúdo
		if( !$conteudo ) {
			return [
				'available' => 0,
				'total' => 0,
				'results' => [
					'imageRepository' => $imageRepository,
					'content' => null
				]
			];
		}
		
		//PRECISO AGORA TRATAR O CONTEUDO
		$contentDataSource = \Sh\ModuleFactory::getModuleDataSourceByAlias($imageRepository['idDataSource']);
		$contentData = array();
		$contentData['id'] 		= $conteudo[ $contentDataSource->getPrimaryKey(false)->getId() ];
		$contentData['nome'] 	= $conteudo[ $contentDataSource->getPrimaryName(false)->getId() ];
		
		//RETORNO O IMAGE.REPOSITORY E O CONTEUDO
		return [
			'available' => 1,
			'total' => 1,
			'results' => [
				'imageRepository' => $imageRepository,
				'content' => $contentData
			]
		];
	
		return $imageRepository;
	}
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler para substituir o add padrão
 * Este irá determinar se o conteudo pode ter um imageRepository e irá criar caso necessário
 * 
 * Mapa de erros:
 * 		IR000 -> DataSource não aceita ImageRepository
 * 		IR001 -> DataSource não encontrado
 *
 */
class criarAlbum extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//VERIFICAR SE DATASOURCE ACEITA IMAGE.REPOSITORY
		list($idModule, $idDataSource) = explode('/', $data['idDataSource']);
		$dataSource = \Sh\ModuleFactory::getModuleDataSource($idModule, $idDataSource);
		if( !$dataSource ) {
			throw new \Sh\ActionException(array(
				'code' => 'IR001',
				'message' => 'DataSource fornecido não foi encontrado'
			));
		}
		if( !$dataSource->hasImageRepository() ) {
			throw new \Sh\ActionException(array(
					'code' => 'IR000',
					'message' => 'O DataSource fornecido não aceita imageRepository para seus conteudos'
			));
		}
		
		//BUSCAR IMAGE.REPOSITORY JÁ CRIADO
		$imageRepository = \Sh\ContentProviderManager::loadContent('imageRepository/albumPorConteudo', array(
			'idContent'=>$data['idContent'],
			'idDataSource'=>$data['idDataSource']
		));
		
		//já temos o imageRepository cadastrado
		if( $imageRepository['total'] > 0 ) {
			$imageRepository = reset($imageRepository['results']);
			unset($imageRepository['publicationMetadata']);
		}
		//preciso cadastrar um novo
		else {
			//CRIAR IMAGE.REPOSITORY
			$imageRepository = array();
			$imageRepository['id'] = \Sh\Library::getUniqueId();
			$imageRepository['idContent'] = $data['idContent'];
			$imageRepository['idDataSource'] = $data['idDataSource'];
			$imageRepository['idCapa'] = null;
			$imageRepository['legenda'] = null;
			$imageRepository['quantidade'] = 0;
			$response = \Sh\ContentActionManager::doPrimitiveAction('imageRepository/album', 'add', $imageRepository, $this->connection);
			\Sh\Library::actionResponseCheck($response);
		}
		
		//RETORNAR
		return array(
			'status' => true,
			'code' => null,
			'data' => $imageRepository,
			'message' => null
		);
		
	}
	
}

/**
 * @author Guilherme
 *
 * ActionHandler para cadastrar uma nova imagem a um IR
 * 
 */
class adicionarImagem extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//PEGO O ULTIMO ELEMENTO DO ALBUM PARA ATUALIZO ELE COM O ID DA NOVA IMAGEM
		//se eu não encontrar ultimo, assumo que a minha é a primeira
		$ultimaImagem = \Sh\ContentProviderManager::loadContent('imageRepository/ultimaImagemDoAlbum', $data, null, $this->connection);
		if( $ultimaImagem['total'] > 0 ) {
			$ultimaImagem = reset($ultimaImagem['results']);
		}
		else {
			$data['primeiro'] = '1';
			$ultimaImagem = false;
		}
		
		//CADASTRANDO A IMAGEM
		$data['id'] = \Sh\Library::getUniqueId();
		$responsePicture = \Sh\ContentActionManager::doPrimitiveAction('imageRepository/picture', 'add', $data, $this->connection);
		\Sh\Library::actionResponseCheck($responsePicture);
		
		//ATUALIZO A ULTIMA IMAGEM PARA RECEBER O ID DA NOVA IMAGEM
		if( $ultimaImagem ) {
			$ultimaImagemAtualizar = array(
				'id' => $ultimaImagem['id'],
				'idProximo' => $data['id']
			);
			$reponseUltimaImagem = \Sh\ContentActionManager::doAction('imageRepository/picture_update', $ultimaImagemAtualizar, $this->connection);
			\Sh\Library::actionResponseCheck($reponseUltimaImagem);
		}
		
		//BUSCO O ALBUM
		$album = \Sh\ContentProviderManager::loadContentById('imageRepository/album', $data['idRepository'], $this->connection);
		if( !$album ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Erro ao tentar recuperar o repositório de imagens'
			));
		}
		
		//ATUALIZANDO ALBUM [quantidade]
		$albumAtualizar = array('id'=>$data['idRepository'], 'quantidade'=>(int)$album['quantidade']+1);
		$response = \Sh\ContentActionManager::doAction('imageRepository/album_update', $albumAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//CARREGANDO A IMAGEM CADASTRADA PARA RETORNAR
		$filePicture = \Sh\ContentProviderManager::loadContentById('filePicture/filePicture', $responsePicture['data']['idPicture'], $this->connection);
		unset($filePicture['publicationMetadata']);
		$picture = $responsePicture['data'];
		$picture['pictures'] = $filePicture['pictures'];
		
		//RETORNAR
		return array(
				'status' => true,
				'code' => null,
				'data' => $picture,
				'message' => null
		);
	
	}
	
	/**
	 * @see \Sh\GenericAction::processDataFiles()
	 * 
	 * Customizo este para aceitar os imageMaps do DataSource de origem onde estão sendo inseridos as imagens
	 * Neste irei cadastrar as imagens do idPicture e irei simular um valor já existente para o campo e simular que não foi enviado novo arquivo para o processador original não encontrar problemas
	 */
	protected function processDataFiles ( $data ) {
		
		//CAPTURANDO TODOS OS CAMPOS
		$fields = $this->actionHandler->getDataSource()->getFields(false);
		
		//ITERANDO PELOS CAMPOS DO SOURCE PARA BUSCAR OS DE ARQUIVO
		foreach( $fields as $idField=>$field ) {
			
			//escapando campos que não são de arquivo
			if( !in_array($field->getDataType(), array('image')) ) {
				continue;
			}
			
			//Capturando o repositorio
			$repository = \Sh\ContentProviderManager::loadContentById('imageRepository/album', $data['idRepository']);
			//capturando o dataSource
			$dataSource = \Sh\ModuleFactory::getModuleDataSourceByAlias($repository['idDataSource']);
		
			//CADASTRANDO NOVO ARQUIVO
			//inserindo novo arquivo
			$imageData = array(
				'image'=>$data[$field->getId()], 
				'maps'=>$dataSource->getImageRepositoryMaps()
			);
			$responseDocument = \Sh\ContentActionManager::doAction('filePicture/filePicture_add', $imageData, $this->connection);
			\Sh\Library::actionResponseCheck($responseDocument);
			
			//registrando o dado na posição correta
			$data[$field->getId()] = $responseDocument['data']['id'];
			
		}
		
		return parent::processDataFiles($data);
		
	}
	
}

/**
 * @author Guilherme
 *
 * ActionHandler que irá receber informações de um reposicionamento de imagens e irá efetuar todas as operações necessárias
 *
 */
class reposicionarImagem extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//CARREGANDO AS IMAGENS NECESSÁRIAS
		//criando o filtro pelas 3 necessárias
		$filters = array('id'=>array());
		$filters['id'][] = $data['idImagem'];
		$filters['id'][] = $data['idReferencia'];
		if( $data['idAnterior'] ) { $filters['id'][] = $data['idAnterior']; }
		
		//capturando as imagens
		$imagens = \Sh\ContentProviderManager::loadContent('imageRepository/getImagensPorId', $filters);
		if( $imagens['total'] < 2 || !isset($imagens['results'][$data['idImagem']]) || !isset($imagens['results'][$data['idReferencia']]) ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Imagens de referencia são inválidas para reposicionamento de imagens'
			));
		}
		$imagemMovida = $imagens['results'][$data['idImagem']];
		$imagemReferencia = $imagens['results'][$data['idReferencia']];
		
		//ATUALIZANDO A IMAGEM ANTERIOR PARA RECEBER O PROXIMO DA MOVIDA
		if( $data['idAnterior'] ) {
			//capturando a imagem anterior
			$imagemAnterior = $imagens['results'][$data['idAnterior']];
			//guardando o seu proximo
			$imagemAnteriorAtualizar = array('id'=>$data['idAnterior'], 'idProximo'=>$imagemMovida['idProximo']);
			$response = \Sh\ContentActionManager::doAction('imageRepository/picture_update', $imagemAnteriorAtualizar, $this->connection);
			\Sh\Library::actionResponseCheck($response);
		}
		//se a movida não tiver anterior quer dizer que ela é a primeira, e com isso a proxima dela deve ser a primeira
		else {
			if( $imagemMovida['primeiro'] != 1 ) {
				throw new \Sh\SheerException(array(
					'code' => null,
					'message' => 'Erro Inesperado. A imagem movida não possui imagem anterior e não é a primeira do álbum.'
				));
			}
			$imagemProximaAtualizar = array(
				'id' => $imagemMovida['idProximo'],
				'primeiro'=> 1
			);
			$response = \Sh\ContentActionManager::doAction('imageRepository/picture_update', $imagemProximaAtualizar, $this->connection);
			\Sh\Library::actionResponseCheck($response);
		}
		
		//ATUALIZANDO O PRÓXIMO DA IMAGEM MOVIDA PARA RECEBER O PRÓXIMO DA REFERENCIA
		//sempre coloco ela não sendo a primeira pois só movemos para a direita
		$imagemMovidaAtualizar = array('id'=>$imagemMovida['id'], 'idProximo'=>$imagemReferencia['idProximo'], 'primeiro'=>2);
		$response = \Sh\ContentActionManager::doAction('imageRepository/picture_update', $imagemMovidaAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		$responseReturn = $response['data'];
		
		//ATUALIZANDO O PRÓXIMO DA IMAGEM DE REFERENCIA PARA RECEBER A MOVIDA
		$imagemReferenciaAtualizar = array('id'=>$imagemReferencia['id'], 'idProximo'=>$imagemMovida['id']);
		$response = \Sh\ContentActionManager::doAction('imageRepository/picture_update', $imagemReferenciaAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		
		return [
			'status' => true,
			'code' => null,
			'message'=>null,
			'data' => $responseReturn
		];
		
		
	}
	
}

/**
 * @author Guilherme
 *
 * ActionHandler que irá receber o id de uma foto e irá defini-la como capa do seu album
 */
class marcarCapa extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//validando imagem
		$picture = \Sh\ContentProviderManager::loadContentById('imageRepository/picture', $data['id']);
		if( !$picture ) {
			throw new \Sh\ActionException(array(
				'code' => null,
				'message' => 'Imagem a ser marcada como capa do álbum é inválida'
			));
		}
		
		//ATUALIZANDO ALBUM
		$atualizarAlbum = array(
			'id' => $picture['idRepository'],
			'idCapa' => $picture['id']
		);
		$response = \Sh\ContentActionManager::doAction('imageRepository/album_update', $atualizarAlbum, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		return $response;
		
	}
	
}

/**
 * ActionHandler para remover imagens e também atualizar o album com o novo numero de arquivos
 */
class removerImagem extends \Sh\GenericAction {

	public function doAction($data) {
		
		//CAPTURO A IMAGEM
		$imagem = \Sh\ContentProviderManager::loadContentById('imageRepository/picture', $data['id'], $this->connection);
		if( !$imagem ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Imagem a ser removida é inválida'
			));
		}
		
		//CAPTURO O ALBUM PELO SEU ID
		$album = \Sh\ContentProviderManager::loadContentById('imageRepository/album', $imagem['idRepository'], $this->connection);
		if( !$album ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Álbum da imagem a ser removido não foi encontrado'
			));
		}
		$capaDoAlbum = false;
		
		//ATUALIZO O ALBUM COM O NOVO NÚMERO DE ELEMENTOS E TAMBÉM SUA CAPA
		//atualizo o album primeiro pois temos FK de trava pela capa
		$albumAtualizar = array(
			'id' => $album['id'],
			'quantidade' => $album['quantidade'] - 1
		);
		if( $album['idCapa'] == $imagem['id'] ) {
			$albumAtualizar['idCapa'] = null;
			$capaDoAlbum = true;
		}
		$response = \Sh\ContentActionManager::doAction('imageRepository/album_update', $albumAtualizar, $this->connection);
		
		//REORGANIZAR A LISTA ENCADEADA
		//se ela for a primeira da lista e tiver proximo
		if( $imagem['primeiro'] == 1 && $imagem['idProximo'] ) {
			//preciso colocar a proxima a ela como primeiro
			$proximaImagem = $album['imagens'][$imagem['idProximo']];
			$proximaImagemAtualizar = ['id'=>$imagem['idProximo'], 'primeiro'=>1];
			$response = \Sh\ContentActionManager::doAction('imageRepository/picture_update', $proximaImagemAtualizar, $this->connection);
			\Sh\Library::actionResponseCheck($response);
		}
		//não sendo a primeira da lista
		else {
			//busco a imagem anterior a que estou removendo
			foreach ( $album['imagens'] as $idPicture=>$img ) {
				//sendo a anterior a minha preciso atualizar a anterior recebendo o meu proximo
				if( $img['idProximo'] == $imagem['id'] ) {
					$imagemAnteriorAtualizar = ['id'=>$img['id'], 'idProximo'=>$imagem['idProximo']];
					$response = \Sh\ContentActionManager::doAction('imageRepository/picture_update', $imagemAnteriorAtualizar, $this->connection);
					\Sh\Library::actionResponseCheck($response);
					break;
				}
			}
		}
		
		//REMOVO A FOTO 
		$response = \Sh\ContentActionManager::doPrimitiveAction('imageRepository/picture', 'delete', $data, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//inserindo se é a capa no reotrno
		$response['data']['capaDoAlbum'] = $capaDoAlbum;
		
		return $response;
	}
	
}

/**
 * @author Guilherme
 * 
 * ActionHandler para remover todas as imagens de um IR
 * 
 */
class removerTodasImagem extends \Sh\GenericAction {
	
	public function doAction($data) {
		
		//BUSCANDO ALBUM
		$idRepository = $data['id'];
		$album = \Sh\ContentProviderManager::loadContentById('imageRepository/album', $idRepository, $this->connection);
		if( !$album ) {
			throw new \Sh\SheerException(array(
				'code' => null,
				'message' => 'Álbum inválido para remoção das imagens'
			));
		}
		
		//PRECISO REMOVER A CAPA DO ALBUM ANTES DE DELETAR AS FOTOS
		$albumAtualizar = [
			'id' => $album['id'],
			'idCapa' => null,
			'quantidade' => 0
		];
		$response = \Sh\ContentActionManager::doAction('imageRepository/album_update', $albumAtualizar, $this->connection);
		\Sh\Library::actionResponseCheck($response);
		
		//OPERAR POR CADA IMAGEM REMOVENDO TODAS ELAS
		if( $album['imagens'] ) {
			foreach ( $album['imagens'] as $idPicture=>$picture ) {
				//removendo a imagem
				$response = \Sh\ContentActionManager::doPrimitiveAction('imageRepository/picture', 'delete', array('id'=>$picture['id']), $this->connection);
				\Sh\Library::actionResponseCheck($response);
			}
		}
		
		return [
			'status' => true,
			'code' => null,
			'message' => null,
			'data' => array()
		];
		
	}
	
}
