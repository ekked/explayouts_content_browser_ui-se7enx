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
 *  BR-03 - the link up to the parent location only for locations the user may read
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
class eZDebug { public static function updateSettings( $settings ) {} }
class eZHTTPTool { public static function instance() { return new self(); } }
class ezpI18n { public static function tr( $context, $source ) { return $source; } }
class eZTemplate
{
    public static $variables = array();
    public static function factory() { return new self(); }
    public function setVariable( $name, $value ) { self::$variables[$name] = $value; }
    public function fetch( $uri ) { return ''; }
}
class eZContentObjectTreeNode
{
    /** node id => array( parent id, readable ) */
    public static $nodes = array();
    public $id;
    public function __construct( $id ) { $this->id = $id; }
    public static function fetch( $id ) { return isset( self::$nodes[$id] ) ? new self( $id ) : null; }
    public function canRead() { return self::$nodes[$this->id][1]; }
    public function attribute( $name ) { return $name === 'parent_node_id' ? self::$nodes[$this->id][0] : null; }
}
class expLayoutsContentBrowserCoreBackend
{
    public function __construct( array $a = array(), array $b = array() ) {}
    public function getSubItems( $id, $offset, $limit ) { return array(); }
    public function getSubItemsCount( $id ) { return 0; }
}
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

// BR-03
$browse = function( $nodeId ) use ( $class )
{
    $_SERVER['REQUEST_URI'] = '/explayouts_content_browser_ui/browser/' . $nodeId;
    $view = new $class();
    $result = $view->run( array( 'Params' => array( 'Module' => null, 'LocationNodeID' => $nodeId ) ) );
    return array( eZTemplate::$variables['parent_node_id'], $result );
};
eZContentObjectTreeNode::$nodes = array( 1 => array( 0, false ), 2 => array( 1, true ), 50 => array( 2, true ), 60 => array( 61, true ), 61 => array( 2, false ), 70 => array( 2, false ) );
list( $parent, $result ) = $browse( 50 );
brCheck( $parent === 2 && isset( $result['path'] ), 'BR-03 the readable parent of a readable location (and BR-02 a path)' );
brCheck( $browse( 2 )[0] === 1, 'BR-03 the tree root stays reachable' );
brCheck( $browse( 60 )[0] === 0, 'BR-03 no way up to a parent the user may not read' );
brCheck( $browse( 70 )[0] === 0, 'BR-03 nothing about a location the user may not read' );

echo $failures ? "$failures failed\n" : "all passed\n";
exit( $failures ? 1 : 0 );
