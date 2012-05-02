# URLify for PHP

A PHP port of [URLify.js](https://github.com/django/django/blob/master/django/contrib/admin/static/admin/js/urlify.js)
from the Django project. Handles symbols from Latin languages, Greek, Turkish,
Russian, Ukrainian, Czech, Polish, and Latvian. Symbols it cannot transliterate
it will simply omit.

* Author: [jbroadway](http://github.com/jbroadway)
* License: MIT

## Usage:

To generate slugs for URLs:

```php
<?php

echo URLify::filter (' J\'étudie le français ');
// "jetudie-le-francais"

echo URLify::filter ('Lo siento, no hablo español.');
// "lo-siento-no-hablo-espanol"

?>
```

To simply transliterate characters:

```php
<?php

echo URLify::downcode ('J\'étudie le français');
// "J'etudie le francais"

echo URLify::downcode ('Lo siento, no hablo español.');
// "Lo siento, no hablo espanol."

/* Or use transliterate() alias: */

echo URLify::transliterate ('Lo siento, no hablo español.');
// "Lo siento, no hablo espanol."

?>
```

To extend the character list:

```php
<?php

URLify::add_chars (array (
	'¿' => '?', '®' => '(r)', '¼' => '1/4',
	'¼' => '1/2', '¾' => '3/4', '¶' => 'P'
));

echo URLify::downcode ('¿ ® ¼ ¼ ¾ ¶');
// "? (r) 1/2 1/2 3/4 P"

?>
```

To extend the list of words to remove:

```php
<?php

URLify::remove_words (array ('remove', 'these', 'too'));

?>
```
