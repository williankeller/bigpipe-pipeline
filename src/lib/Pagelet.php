<?php

namespace BigPipe\Lib;

/**
 * Class Pagelet
 */
class Pagelet
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string|null
     */
    private $callback = null;

    /**
     * @var int
     */
    public $priority = 0;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var array
     */
    private $css = [];

    /**
     * @var array
     */
    private $js = [];

    /**
     * @var string
     */
    private $script = '';

    /**
     * @var mixed
     */
    private $bypassContainer = null;

    /**
     * @var boolean
     */
    public $useSpan = false;

    /**
     * @param $id
     * @param null $callback
     * @param int $priority
     * @param array $arguments
     */
    public function __construct($id, $callback = null, $priority = 10, $arguments = [])
    {
        $this->id = $id;
        $this->callback = $callback;
        $this->arguments = $arguments;

        BigPipe::addPagelet($id, $this);

        $this->priority = $priority;

        if (!BigPipe::isEnabled()) {
            $this->bypassContainer = $this->executeCallback();
        }
    }

    /**
     * @param array $files
     * @return void
     */
    public function addCss(array $files): void
    {
        foreach ($files as $file) {
            $this->css[] = $file;
        }
    }

    /**
     * @param string $str
     * @param bool $isFile
     * @return void
     */
    public function addContent(string $str, $isFile = false): void
    {
        if ($isFile === true) {
            ob_start();
            /** @noinspection PhpIncludeInspection */
            include $str;
            $this->content .= ob_get_contents();
            ob_end_clean();
        } else {
            $this->content .= $str;
        }
    }

    /**
     * Add the javascript files.
     *
     * @param array $files
     * @return void
     */
    public function addJs(array $files): void
    {
        foreach ($files as $file) {
            if (BigPipe::isEnabled()) {
                $this->js[] = $file;
            }
        }
    }

    /**
     * Add javascript to the inline execution (without the <script></script>tags)
     *
     * @param string $code
     * @return void
     */
    public function addJsScript(string $code): void
    {
        $this->script .= $code;
    }

    /**
     * In case Bigpipe is disabled, the callback is called immediately and the results
     * are stored in this variable for later use in __toString ().
     *
     * @return mixed
     */
    protected function executeCallback()
    {
        if ($this->callback == null) {
            return null;
        }
        if ($this->arguments == null) {
            return call_user_func($this->callback);
        }
        return call_user_func_array($this->callback, $this->arguments);
    }

    /**
     * @return array
     */
    protected function getContent(): array
    {
        if ($this->bypassContainer == null) {
            $ret = $this->executeCallback();
        } else {
            $ret = $this->bypassContainer;
        }
        $data = [];

        if ($ret instanceof ViewBox) {
            $data['script'] = $ret->getJavascript();
            $data['content'] = $ret->getContent(false);
        } else {
            $data['content'] = '' . $ret;
        }
        $data['content'] .= $this->content;

        return $data;
    }

    /**
     * @return array
     */
    public function renderData(): array
    {
        $data = $this->getContent();

        $data['script'] = '';
        $data['id'] = $this->id;
        $data['css'] = $this->css;
        $data['js'] = $this->js;
        $data['script'] .= $this->script;

        return $data;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (BigPipe::isEnabled()) {
            if ($this->useSpan) {
                return sprintf('<span id="%s"></span>', $this->id);
            }
            return sprintf('<div id="%s"></div>', $this->id);
        }
        $data = $this->getContent();
        $content = $data['content'];

        if ($data['script']) {
            BigPipe::addDomLoaded($data['script']);
        }
        return $content;
    }
}
