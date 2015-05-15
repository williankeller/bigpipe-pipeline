<?php

use BigPipe\Lib;

/** @var Lib\Pagelet $element */
$element = new Lib\Pagelet('header', null, 10);

// Adding styles to the head.
$element->addCss(['pagelets/header/css/style.css']);

// Adding content to the div.
$element->addContent('Header content');

// Adding javascript files to the end of the page.
$element->addJs(['pagelets/header/js/script.js']);

?>
<header>
    <div><?= /** Printing Pagelet elements */ $element; ?></div>
</header>