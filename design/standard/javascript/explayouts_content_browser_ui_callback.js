/**
 * explayouts_content_browser_ui: the answer of the content browser to a popup selection (return_uri=js,
 * templates/explayouts_content_browser_ui/js_callback.tpl).
 *
 * The chosen item is in the data-* attributes of #explayouts-content-browser-callback: it is handed to the
 * setRuleTargetValue( nodeId, name, pathWithNames, field ) of the page that opened the browser (or of the parent
 * page, when the browser is in a frame), then the window closes. A file, so it runs under a Content Security Policy
 * without 'unsafe-inline'.
 */
(function () {
    var item = document.getElementById( 'explayouts-content-browser-callback' );
    if ( !item ) { return; }
    var nodeId = parseInt( item.getAttribute( 'data-node-id' ), 10 ),
        name = item.getAttribute( 'data-name' ) || '',
        pathWithNames = item.getAttribute( 'data-path-with-names' ) || '',
        field = item.getAttribute( 'data-field' ) || '';

    if ( window.opener && typeof window.opener.setRuleTargetValue === 'function' )
    {
        window.opener.setRuleTargetValue( nodeId, name, pathWithNames, field );
    }
    else if ( window.parent && typeof window.parent.setRuleTargetValue === 'function' )
    {
        window.parent.setRuleTargetValue( nodeId, name, pathWithNames, field );
    }

    window.close();
})();
