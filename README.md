
## BigPipe: Pipelining web pages for high performance
[![Build Status](https://travis-ci.org/williankeller/bigpipe-pipeline.svg?branch=master)](https://travis-ci.org/williankeller/bigpipe-pipeline) 
[![Packagist](https://img.shields.io/packagist/v/wkeller/bigpipe-pipeline.svg)](https://packagist.org/packages/wkeller/bigpipe-pipeline) 
[![Downloads](https://img.shields.io/packagist/dt/wkeller/bigpipe-pipeline.svg)](https://packagist.org/packages/wkeller/bigpipe-pipeline)

Bigpipe is a feature originally created by facebook: 
[BigPipe: Pipelining web pages for high performance.](https://engineering.fb.com/web/bigpipe-pipelining-web-pages-for-high-performance/)
The general idea is to break down web pages into small reusable pieces of functionality 
called Pagelets and separate them into various stages of execution within web servers and browsers. This enables 
progressive front-end rendering and results in exceptional front-end performance.


### Our library code:
Most web frameworks are based on a request and response pattern, one request arrives, we process the data and output 
of a model. But before we can issue the model we have to wait until all the data has been received before the model 
can be processed. When you receive your first batch of data, why not send it directly to the browser so that it can 
start downloading the required CSS, JavaScript and processing it.


### Install via composer (recommended)
```sh
composer create-project wkeller/bigpipe-pipeline
```

### Methods to use it:
* **addContent()** _(add content to the some DOM element)_
* **addCss()** _(add the CSS no to the head of the page)_
* **addJS()** _(add JavaScript file to the footer of the page)_
* **addJsScript()** _(add inline JavaScript content)_

### Some working examples:

**Adding static text content:**
```php
// Initiate Pagelet (creates new div with ID)
$element = new Pagelet('static-text');

// Adding the text content inside of the DIV element
$element->addContent('Static text content inside of the "static-text" DIV element.');

// Priting Pagelet element
echo $element;
```

**Adding external file:**
Possible files to be loaded: PHP, HTML and more.
```php
// Initiate Pagelet (creates new div with ID)
$element = new Pagelet('it-is-a-file');

// Adding the content of the file inside of the DIV element
$element->addContent('path/to/file/filename.php', true);

// Priting Pagelet element
echo $element;
```

**Adding CSS no head**
```php
// Initiate Pagelet
$element = new Pagelet('css');
        
// Adding style files to the head
$pagelet->addCss([
  'path/to/file/style.css',
  'path/to/file/fonts.css'
]);

// Priting Pagelet element
echo $element;
```

**Adding JavaScript files to the footer of the page:**
```php
// Initiate Pagelet
$element = new Pagelet('javascript');
        
// Adding JavaScript files to the footer
$pagelet->addJS([
  'path/to/file/jQuery.js',
  'path/to/file/script.js'
]);

// Priting Pagelet element
echo $element;
```

**Adding inline JavaScript content:**
```php
// Initiate Pagelet
$element = new Pagelet('javascript-inline');
        
// Adicionando javascript inline
$pagelet->addJsScript("$('static-text').innerHTML = 'Changing static content';");

// Priting Pagelet element
echo $element;
```

### Priority:
In BigPipe, JavaScript resource is given lower priority than CSS and page content. Therefore, BigPipe won’t start 
downloading JavaScript for any pagelet until all pagelets in the page have been displayed.

With that, it's possible to set priority to the Pagelets you are loading. Let's say you'd like to load your footer
content as last content:

```php
// Initiate Pagelet
$element = new Pagelet('footer', null, 30);
        
// Adding JavaScript files to the footer
$pagelet->addJS(['path/to/file/footer.js']);

// Adding the text content inside of the DIV element
$element->addContent('Footer content goes here.');

// Priting Pagelet element
echo $element;
```

In this case, `footer` will have priority `30`. All the Pagelets bofore this number will be loaded first.

### Contribution
Want to contribute to this extension? The quickest way is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).


### Support
If you encounter any problems or bugs, please open an issue on [GitHub](https://github.com/williankeller/bigpipe-pipeline/issues).
Need help setting up or want to customize this extension to meet your business needs? Please open an issue and if we like your idea we will add ad a feature.
