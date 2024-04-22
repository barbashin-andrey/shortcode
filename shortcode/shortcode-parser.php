<?php

/**
 * Shortcode parser (created for ShortcodeHandler)
 */
class ShortcodeParser {
    private static function getShortcodeRegex($shortcode_tags): string {

        $tagregexp = implode( '|', array_map( 'preg_quote',$shortcode_tags ) );

        /*
         * WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag().
         * Also, see shortcode_unautop() and shortcode.js.
         */

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        return '/\\['                             // Opening bracket.
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]].
            . "($tagregexp)"                     // 2: Shortcode name.
            . '(?![\\w-])'                       // Not followed by word character or hyphen.
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag.
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash.
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket.
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash.
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag...
            .     '\\]'                          // ...and closing bracket.
            . '|'
            .     '\\]'                          // Closing bracket.
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags.
            .             '[^\\[]*+'             // Not an opening bracket.
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag.
            .                 '[^\\[]*+'         // Not an opening bracket.
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag.
            .     ')?'
            . ')'
            . '(\\]?)/';                          // 6: Optional second closing bracket for escaping shortcodes: [[tag]].
        // phpcs:enable
    }

    private static function getShortcodeAttsRegex() {
        return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)/';
    }

    private static function shortcodeParseAtts($text ) {
        $atts    = array();
        $pattern = self::getShortcodeAttsRegex();
        $text    = preg_replace( "/[\x{00a0}\x{200b}]+/u", ' ', $text );
        if ( preg_match_all( $pattern, $text, $match, PREG_SET_ORDER ) ) {
            foreach ( $match as $m ) {
                if ( ! empty( $m[1] ) ) {
                    $atts[ strtolower( $m[1] ) ] = stripcslashes( $m[2] );
                } elseif ( ! empty( $m[3] ) ) {
                    $atts[ strtolower( $m[3] ) ] = stripcslashes( $m[4] );
                } elseif ( ! empty( $m[5] ) ) {
                    $atts[ strtolower( $m[5] ) ] = stripcslashes( $m[6] );
                } elseif ( isset( $m[7] ) && strlen( $m[7] ) ) {
                    $atts[] = stripcslashes( $m[7] );
                } elseif ( isset( $m[8] ) && strlen( $m[8] ) ) {
                    $atts[] = stripcslashes( $m[8] );
                } elseif ( isset( $m[9] ) ) {
                    $atts[] = stripcslashes( $m[9] );
                }
            }

            // Reject any unclosed HTML elements.
            foreach ( $atts as &$value ) {
                if ( str_contains( $value, '<' ) ) {
                    if ( 1 !== preg_match( '/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value ) ) {
                        $value = '';
                    }
                }
            }
        }

        return $atts;
    }

    /**
     * It will scratch specific shortcodes from your content
     * @param string $content Content to parse
     * @param array $shortcode_tags Shortcode tags to catch (coming from addShortcode method)
     * @return array Array of parsed content.
     *               Contains associative arrays of shortcuts:
     *                  First element is a matched shortcut.
     *                  Second element is a name of shortcut.
     *                  Third element is an array of shortcut's attributes.
     */
    public static function parse($content, $shortcode_tags): array {
        preg_match_all(self::getShortcodeRegex($shortcode_tags), $content, $shortcodes, PREG_SET_ORDER);
        $result = array();
        foreach ($shortcodes as $shortcode) {
            $shortcode_matched = $shortcode[0];
            $shortcode_name = $shortcode[2];
            $shortcode_atts = self::shortcodeParseAtts($shortcode[3]);
            $result[] = array(
                "matched" => $shortcode_matched,
                "name" => $shortcode_name,
                "atts" => $shortcode_atts
            );
        }
        return $result;
    }
}