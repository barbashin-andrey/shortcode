<?php

/**
 * Class to handle shortcodes in your (html or any other...) content
 */
class ShortcodeHandler {
    private array $shortcodes;

    /**
     * e.g. addShortcode('myShortcode', 'myFunc', ['some args for myFunc'])
     * @param string $shortcode Shortcode to catch
     * @param callable $callback Callback function for your shortcode.
     *                           Should return string, but not necessarily.
     *                           If string returned, content (passed to handle method) will be modified.
     * @param array $args Arguments for callback.
     * @return void
     */
    public function addShortcode($shortcode, $callback, $args = array()) {
        $this->shortcodes[$shortcode] = array("callback" => $callback, "args" => $args);
    }

    /**
     * Add some shortcodes before calling it
     * @param string $content Content to parse
     * @return string Content that was modified by callback functions
     */
    public function handle($content) : string {
        $shortcode_tags = array_keys($this->shortcodes);
        $parsed = ShortcodeParser::parse($content, $shortcode_tags);

        foreach ($parsed as $shortcode) {
            $current_shortcode = $this->shortcodes[$shortcode["name"]];
            $shortcode["args"] = $current_shortcode["args"];

            $result = call_user_func($current_shortcode["callback"], $shortcode);
            $content = str_replace($shortcode["matched"], $result, $content);
        }
        return $content;
    }
}