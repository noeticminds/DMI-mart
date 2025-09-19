<?php
require_once JGM_PLUGIN_PATH . 'vendor/scssphp/scss.inc.php';

use ScssPhp\ScssPhp\Compiler;

$scss = new Compiler();

$scss->setImportPaths(JGM_PLUGIN_PATH . '/assets/stylesheets/');

$css = $scss->compile(
  '@import "mixins.scss"; @import "fonts.scss"; @import "media-queries.scss"; @import "judgeme.scss";'
);

print '<style type="text/css">'.$css.'</style>';
?>
