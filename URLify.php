<?php

/**
 * A PHP port of URLify.js from the Django project
 * (https://github.com/django/django/blob/master/django/contrib/admin/static/admin/js/urlify.js).
 * Handles symbols from Latin languages, Greek, Turkish, Bulgarian, Russian,
 * Ukrainian, Czech, Polish, Romanian, Latvian, Lithuanian, Vietnamese, Arabic,
 * Serbian, Azerbaijani, Kazakh and Slovak. Symbols it cannot transliterate
 * it will simply omit.
 *
 * Usage:
 *
 *     echo URLify::filter (' J\'étudie le français ');
 *     // "jetudie-le-francais"
 *
 *     echo URLify::filter ('Lo siento, no hablo español.');
 *     // "lo-siento-no-hablo-espanol"
 */
class URLify
{
    /**
     * The language-mapping array.
     *
     * ISO 639-1 codes: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     *
     * @var array
     */
    public static $maps = [];

    /**
     * List of words to remove from URLs.
     */
    public static $remove_list = array (
        'a', 'an', 'as', 'at', 'before', 'but', 'by', 'for', 'from',
        'is', 'in', 'into', 'like', 'of', 'off', 'on', 'onto', 'per',
        'since', 'than', 'the', 'this', 'that', 'to', 'up', 'via',
        'with'
    );

	/**
	 * Add new characters to the list. `$map` should be a hash.
     * @param array $map
     * @param string|null $language
	 */
	public static function add_chars ($map, string $language = null)
    {
        $language_key = $language ?? uniqid('urlify', true);

        if (isset(self::$maps[$language_key])) {
            self::$maps[$language_key] = array_merge($map, self::$maps[$language_key]);
        } else {
            self::$maps[$language_key] = $map;
        }
	}

	/**
	 * Append words to the remove list. Accepts either single words
	 * or an array of words.
     * @param mixed $words
	 */
	public static function remove_words ($words)
    {
		$words = is_array ($words) ? $words : array ($words);
		self::$remove_list = array_unique (array_merge (self::$remove_list, $words));
	}

	/**
	 * Transliterates characters to their ASCII equivalents.
     * $language specifies a priority for a specific language.
     * The latter is useful if languages have different rules for the same character.
     * @param string $text
     * @param string $language
     * @return string
	 */
	public static function downcode ($text, $language = "")
    {
        foreach (self::$maps as $mapsInner) {
            foreach ($mapsInner as $orig => $replace) {
                $text = str_replace($orig, $replace, $text);
            }
        }

        $langSpecific = \voku\helper\ASCII::charsArrayWithOneLanguage($language, true);
        if (!empty($langSpecific)) {
            $text = str_replace(
                $langSpecific['orig'],
                $langSpecific['replace'],
                $text
            );
        }
        foreach (\voku\helper\ASCII::charsArrayWithMultiLanguageValues(true) as $replace => $orig) {
            $text = str_replace($orig, $replace, $text);
        }

        return $text;
	}

	/**
	 * Filters a string, e.g., "Petty theft" to "petty-theft"
	 * @param string $text The text to return filtered
	 * @param int $length The length (after filtering) of the string to be returned
	 * @param string $language The transliteration language, passed down to downcode()
	 * @param bool $file_name Whether there should be and additional filter considering this is a filename
	 * @param bool $use_remove_list Whether you want to remove specific elements previously set in self::$remove_list
	 * @param bool $lower_case Whether you want the filter to maintain casing or lowercase everything (default)
	 * @param bool $treat_underscore_as_space Treat underscore as space, so it will replaced with "-"
     * @return string
	 */
	public static function filter ($text, $length = 60, $language = "", $file_name = false, $use_remove_list = true, $lower_case = true, $treat_underscore_as_space = true)
    {
		$text = self::downcode ($text,$language);

		if ($use_remove_list) {
			// remove all these words from the string before urlifying
			$text = preg_replace ('/\b(' . implode ('|', self::$remove_list) . ')\b/i', '', $text);
		}

		// if downcode doesn't hit, the char will be stripped here
		$remove_pattern = ($file_name) ? '/[^_\-.\-a-zA-Z0-9\s]/u' : '/[^\s_\-a-zA-Z0-9]/u';
		$text = preg_replace ($remove_pattern, '', $text); // remove unneeded chars
		if ($treat_underscore_as_space) {
		    	$text = str_replace ('_', ' ', $text);             // treat underscores as spaces
		}
		$text = preg_replace ('/^\s+|\s+$/u', '', $text);  // trim leading/trailing spaces
		$text = preg_replace ('/[-\s]+/u', '-', $text);    // convert spaces to hyphens
		if ($lower_case) {
			$text = strtolower ($text);                        // convert to lowercase
		}

		return trim (substr ($text, 0, $length), '-');     // trim to first $length chars
	}

	/**
	 * Alias of `URLify::downcode()`.
	 */
	public static function transliterate ($text)
    {
		return self::downcode ($text);
	}
}
