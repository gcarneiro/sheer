CKEDITOR.plugins.add( 'BGSVideo', {
    icons: 'BGSVideo',
    init: function( editor ) {

		editor.addCommand( 'insertBGSVideo', {
		    exec: function( editor ) {    
				var options = 'width=650, height=500, top=200, left=200, scrollbars=yes, status=no, toolbar=no, location=0, directories=0, menubar=0, resizable=0, fullscreen=0';
				richTextContentControl.bgsEditors[editor.id] = editor;
				var janela = window.open('main.php?bgsModulo=video.escolherVideoPop&pop=1', editor.id, options);
		    }
		});
		
		editor.ui.addButton( 'BGSVideo', {
		    label: 'Inserir video',
		    command: 'insertBGSVideo',
		    toolbar: 'insert'
		});
    }
});