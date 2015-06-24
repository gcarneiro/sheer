<?php
namespace Sh;

/**
 * @author Guilherme
 * 
 * Processador de imagens padrao do sheer
 * Este processador irá calcular as dimensões das imagens a partir das dimensões passadas no mapa e irá recalcular o tamanho da imagem
 *
 */
class ImageProcessor extends \Sh\GenericImageProcessor {
	
	public function process() {
		
		//Capturando dimensoes iniciais
		$imageGeometry = $this->image->getimagegeometry();
		//decidindo se devemos redimensionar
		$doResize = ($this->pictureMap['width'] < $imageGeometry['width'] && $this->pictureMap['width'] > 0);
		$doResize = $doResize || ($this->pictureMap['height'] < $imageGeometry['height'] && $this->pictureMap['height'] > 0);
		
		//Só devo redimensionar se alguma dimensão explode a que tenho na imagem enviada, para manter o máximo
		if( $doResize ) {
			//definindo bestfit - faço isso pois se alguma dimensão for 0, o iMagick irá calcula-la *somente* se o bestFit for false.
			//o bestFit só funciona com as duas dimensões setadas
			$bestFit = false;
			if( $this->pictureMap['width'] > 0 && $this->pictureMap['height'] > 0 ) {
				$bestFit = true;
			}
			$resized = $this->image->scaleImage($this->pictureMap['width'], $this->pictureMap['height'], $bestFit);
			if( !$resized ) {
				throw new \Sh\SheerException(array(
						'code' => null,
						'message' => 'Erro ao redimensionar arquivo de imagem'
				));
			}
		}
		
		return $this->image;
		
	}
	
}