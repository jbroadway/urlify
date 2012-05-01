# URLify for PHP

A PHP port of [URLify.js](https://github.com/django/django/blob/master/django/contrib/admin/static/admin/js/urlify.js)
from the Django project. Handles symbols from Latin languages, Greek, Turkish,
Russian, Ukrainian, Czech, Polish, and Latvian. Symbols it cannot transliterate
it will simply omit.

* Author: [jbroadway](http://github.com/jbroadway)
* License: MIT

Usage:

```php
<?php

echo URLify::filter (' J\'étudie le français ');
// "jetudie-le-francais"

echo URLify::filter ('Lo siento, no hablo español.');
// "lo-siento-no-hablo-espanol"

?>
```
