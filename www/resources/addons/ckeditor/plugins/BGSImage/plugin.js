CKEDITOR.plugins.add( 'BGSImage', {
    icons: 'BGSImage',
    init: function( editor ) {

		editor.addCommand( 'insertBGSImage', {
		    exec: function( editor ) {    
				var options = 'width=650, height=500, top=200, left=200, scrollbars=yes, status=no, toolbar=no, location=0, directories=0, menubar=0, resizable=0, fullscreen=0';
				richTextContentControl.bgsEditors[editor.id] = editor;
				var janela = window.open('main.php?bgsModulo=imagem.escolherImagemPop&pop=1', editor.id, options);
		    }
		});
		
		editor.ui.addButton( 'BGSImage', {
		    label: 'Inserir imagem',
		    command: 'insertBGSImage',
		    toolbar: 'insert'
		});
    }
});