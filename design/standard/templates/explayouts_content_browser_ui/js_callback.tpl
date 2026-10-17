{* Content browser JS callback for popup selection: the chosen item in data-* attributes, handed to the opener's
   setRuleTargetValue() by design/standard/javascript/explayouts_content_browser_ui_callback.js (no inline script) *}
<div id="explayouts-content-browser-callback" hidden="hidden"
     data-node-id="{$selected_item.node_id|wash}"
     data-name="{$selected_item.name|wash}"
     data-path-with-names="{$selected_item.path_with_names|wash}"
     data-field="{$field|wash}"></div>
{ezscript( array( 'explayouts_content_browser_ui_callback.js' ) )}
