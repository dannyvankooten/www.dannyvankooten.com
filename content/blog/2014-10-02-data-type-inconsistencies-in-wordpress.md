+++
title = "Data type inconsistencies in WordPress"
date = 2014-10-02 13:38:15
+++

Yesterday I [tweeted about data type inconsistencies in WordPress plugins](https://twitter.com/DannyvanKooten/status/517432423475589120), something that has been bothering me for a while now.

Inconsistent data types is quite common in PHP development as it is a "weak typed" language. In PHP version 5.3 and up, you can force the type of some non-scalar function parameters. 

[Hack](https://hacklang.org/) further improved on this by introducing **type annotations**, which _allow for PHP code to be explicitly typed on parameters, class member variables and return values_. Very cool.

Unfortunately, both improvements can not be used when developing for WordPress. Yet.

Plugin developers, please make sure your functions __always return the same data type__. If your function returns either a boolean, an array or a string, you are asking for trouble.

> _If your function returns either a boolean, an array or a string, it smells and you need to refactor._

Either you or someone else working with your code will run into a bug later and wonder why. This is even more important when you are allowing third-party developers to filter your function returns.

We have all seen errors like this far too often..

- [Fatal error: Cannot use string offset as an array..](https://wordpress.org/search/cannot+use+string+offset+as+an+array)
- [Warning: strpos() expects parameter 1 to be string, array given..](https://wordpress.org/search/strpos+expects+first+parameter+to+be+a+string)
- [Notice: Trying to get property of non-object..](https://wordpress.org/search/Trying+to+get+property+of+non-object)



Consistency is so important. In everything.
