<?php
/**
 * The browser view of the content browser, without the kernel: where it sends the choice (return_uri) and its path.
 *
 * Run directly with:
 *   php tests/explayoutscontentbrowseruibrowsertest.php
 *
 *  BR-01 - return_uri is "js" or a path on this site; another host, a scheme or control characters are dropped
 *          (the choice was redirected to any address the link named)
 *  BR-02 - the view sets a path, so the page has a title
 */

$failures = 0;
function brCheck( $ok, $message )
{
    global $failures;
    echo ( $ok ? 'PASS' : 'FAIL' ), ": $message\n";
    if ( !$ok )
        $failures++;
}

eval( 'namespace Exponential\\Runnable; abstract class ModuleView { abstract public function run( array $scope ); protected function viewResult( $result, $returned ) { return empty( $result ) ? $returned : $result; } }' );
$file = dirname( __DIR__ ) . '/classes/runnable/views/explayouts_content_browser_ui/browser.php';
require_once $file;
$class = '\\Exponential\\View\\Extension\\ExplayoutsContentBrowserUi\\ExplayoutsContentBrowserUi\\Browser';

// BR-01
foreach ( array( 'js', '/content/edit/12', '/explayouts_ui_api/app#layout/3', '/x?a=1&amp;b=2' ) as $kept )
    brCheck( $class::safeReturnUri( $kept ) === $kept, "BR-01 kept: $kept" );
foreach ( array( 'https://evil.example/', '//evil.example/x', '/\\evil.example', 'javascript:alert(1)', 'content/edit/1', "/x\nLocation: y", '', array( '/x' ) ) as $refused )
    brCheck( $class::safeReturnUri( $refused ) === '', 'BR-01 refused: ' . json_encode( $refused ) );

// BR-02
$source = (string)file_get_contents( $file );
brCheck( strpos( $source, "\$Result['path']" ) !== false, 'BR-02 the view sets a path' );
brCheck( strpos( $source, "self::safeReturnUri( \$nglQuery( 'return_uri'" ) !== false, 'BR-01 the view reads return_uri through safeReturnUri()' );

echo $failures ? "$failures failed\n" : "all passed\n";
exit( $failures ? 1 : 0 );
