require.config({
    paths: {
    	'highlight': 'scripts/highlight/highlight.pack'
    }
});

define('sheer/docs', ['sheer', 'highlight'], function (sheer, hljs) {
	
	hljs.initHighlightingOnLoad();
	
});