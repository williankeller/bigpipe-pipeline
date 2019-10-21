<?php
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * This file is jus an example how you can structure the executions.
 * In your project, it's better to initiate the Pagelet class using DI.
 */

use BigPipe\Lib;

// Initiating Pagelet element.
$element = new Lib\Pagelet('content', null, 20);

// Load Header content.
$element->addContent('pagelets/header/content.php', true);

// Adding content to the page.
$element->addContent('Main content loaded.');

// Load footer content
$element->addContent('pagelets/footer/content.php', true);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>BigPipe: Pipelining web pages for high performance built in PHP.</title>
        <script src="../src/js/prototype.js"></script>
        <script src="../src/js/bigpipe.js"></script>
    </head>
    <body>
        <?= /** Printing Pagelet elements */ $element; ?>
    </body>
<?php
// Rendering Bigpipe whic is also responsible to add the closing html tag.
Lib\BigPipe::render();
