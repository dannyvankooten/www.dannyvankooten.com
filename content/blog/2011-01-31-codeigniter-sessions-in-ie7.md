+++
title = "CodeIgniter Sessions in IE7"
date = 2011-01-31 07:53:31
+++

Yesterday, while working on a fairly simple application that required a login functionality I came across a very strange problem with CodeIgniter and sessions in Internet Explorer 7. While testing everything seemed to work fine in all browsers, even in IE6 (I use IETester to test for the older versions of IE).

### CodeIgniter Sessions not working in IE7
When showing the application to some friends it turned out that they were not able to log in. 

I thought that maybe their browser (IE7, yep..) was blocking cookies so I asked them to lower their security setting. No luck.

After checking my code and some more testing I excluded my code from being the problem. 

A few google queries later I found out that more CodeIgniter users have been experiencing the same problem with Internet Explorer 7 and CodeIgniter sessions not being stored.

### IE7 and the CI_Session class
Since CodeIgniter sessions work fine in some IE7 browsers and fail miserably in others there currently seems to be no definite fix for this problem. 

#### Underscore in the CodeIgniter session cookie
One thing that at least seems related it is the underscore `_` in the cookie name that stores the session variables.

The default CI cookie name is 'ci_session'. Renaming that to `cisession`, without the underscore, did the trick for me.

```php?start_inline=1
$config['sess_cookie_name'] = 'cisession'; // note: no more underscore
```

#### Server time setting

If that didn't do the trick for you you might try checking you're server's time settings. When cookies are being stored they are given a lifetime. It could be that you're server's time is a few hours behind and thus cookies will be removed immediately since their lifetime is already over.

#### Native PHP sessions

The last thing you can do is to drop the CodeIgniter session library and start using PHP's default session handling using `session_start()`, `$_SESSION[]`, `unset` and `session_destroy`. 

Have a look at the <a href="https://codeigniter.com/wiki/Native_session/">CodeIgniter Native Session Library</a> if you really need to use a library to handle your sessions.