<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{'Content browser'|i18n( 'explayouts_content_browser_ui/browser' )}</title>
    <link rel="stylesheet" type="text/css" href={'stylesheets/explayouts_content_browser_ui/popup.css'|ezdesign} />
</head>
{* The content browser on its own, for a dialog or popup that hands the chosen location back by script
   (browser view with return_uri=js). The page that opens it may set data-theme on this document. *}
<body class="ngl-cb-popup">
{$module_result.content}
</body>
</html>
