<?php

namespace BigPipe\Lib;

/**
 * Class BigPipe
 */
class BigPipe
{

    /**
     * @var array
     */
    private static $pagelets = [];

    /**
     * @var int
     */
    private static $pageletCount = 0;

    /**
     * @var null
     */
    private static $enabled = null;

    /**
     * @var null
     */
    private static $topController = null;

    /**
     * @var array
     */
    private static $javascripts = [];

    /**
     * Checks if BigPipe render is enable.
     *
     * @static
     * @return bool
     */
    public static function isEnabled(): bool
    {
        if (self::$enabled === null) {
            self::$enabled = self::checkForEnabling();
        }
        return self::$enabled;
    }

    /**
     * Checks whether we should allow BigPipe or not.
     * This uses "Browser" file to parse the user's browser.
     *
     * @static
     * @return bool
     */
    private static function checkForEnabling(): bool
    {
        if (isset($_REQUEST['bigpipe'])) {
            return (bool) $_REQUEST['bigpipe'];
        }
        if (function_exists('feature_present') && !feature_present('bigpipe')) {
            return false;
        }
        if (self::isBot()) {
            return false;
        }
        return true;
    }

    /**
     * Disable BigPipe.
     *
     * @return void
     */
    public static function disable(): void
    {
        self::$enabled = false;
    }

    /**
     * @param string $id
     * @param Pagelet $pagelet
     * @return void
     */
    public static function addPagelet($id, Pagelet $pagelet): void
    {
        self::$pagelets[$pagelet->priority][$id] = $pagelet;
        self::$pageletCount++;
    }

    /**
     * If your codebase contains multiple hierarchical controllers where
     * each controller calls bigpipe then process the functions you can use.
     *
     * Just call BigPipe::registerController($ this); at the beginning of all your controllers
     * and BigPipe::render($ this); in the end, index will only take place when $this is the same
     * as the first registered controller.
     *
     * @static
     * @param $controller
     * @return void
     */
    public static function registerController($controller): void
    {
        if (self::$topController == null) {
            self::$topController = $controller;
        }
    }

    /**
     * Processes all queued pagelets.
     *
     * @param string|null $callingController
     * @return void
     */
    public static function render($callingController = null)
    {
        if ($callingController != null && self::$topController != $callingController) {
            return;
        }
        if (!self::$enabled) {
            foreach (self::$javascripts as $source) {
                echo self::source($source);
            }
            return;
        }
        if (isset($_REQUEST['bigpipe']) && $_REQUEST['bigpipe'] == 2) {
            return;
        }
        flush();

        $timerStopped = false;
        if (!self::$pageletCount) {
            return;
        }
        // Sorts all pagelets according to their priority (highest priority => starts first first)
        ksort(self::$pagelets);
        self::$pagelets = array_reverse(self::$pagelets);
        
        echo '<script>';

        $i = 0;
        foreach (self::$pagelets as $priority => $container) {
            /** @var Pagelet $pagelet */
            foreach ($container as $id => $pagelet) {
                $data = $pagelet->renderData();

                if (++$i >= self::$pageletCount) {
                    $data['is_last'] = true;
                }
                self::printSingleResponse($data);

                if ($pagelet->priority < 10 && !$timerStopped) {
                    global $globalMeas;

                    if ($globalMeas) {
                        $globalMeas->lap('~');
                        $timerStopped = true;
                    }
                }
            }
        }
        echo "</script>";

        self::$enabled = false;

        echo '</html>';

        flush();
    }

    /**
     * Print a single pagelet off and release it.
     *
     * @param array $data
     * @return void
     */
    private static function printSingleResponse(array $data): void
    {
        static $counter = 0;
        $counter++;

        echo "BigPipe.onArrive(" . json_encode($data) . ");";

        flush();
    }

    /**
     * @return bool
     */
    public static function isBot(): bool
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if ($userAgent == "") {
            return true;
        }
        $botList = ["google", "bot",
            "yahoo", "spider",
            "archiver", "curl",
            "python", "nambu",
            "twitt", "perl",
            "sphere", "PEAR",
            "java", "wordpress",
            "radian", "crawl",
            "yandex", "eventbox",
            "monitor", "mechanize",
            "facebookexternal"
        ];

        foreach ($botList as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $source
     */
    public static function addDomLoaded(string $source): void
    {
        self::$javascripts[] = "try { $source } catch (ex) { console.log(ex); }";
    }

    /**
     * Get javascript source as HTML content.
     *
     * @param string $source
     * @return string
     */
    public static function source(string $source): string
    {
        return sprintf('<script>%s</script>', $source);
    }
}
