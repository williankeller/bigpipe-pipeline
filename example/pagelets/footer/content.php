<?php

/**
 * This file is jus an example how you can structure the executions.
 * In your project, it's better to initiate the Pagelet class using DI.
 */

use BigPipe\Lib;

/** @var Lib\Pagelet $element */
$element = new Lib\Pagelet('footer', null, 30);

// Adding styles to the head.
$element->addCss(['pagelets/footer/css/style.css']);

// Adding content to the div.
$element->addContent('Footer content');

// Adding javascript files to the end of the page.
$element->addJs(['pagelets/footer/js/script.js']);

?>
<footer>
    <div><?= /** Printing Pagelet elements */ $element; ?></div>
</footer>