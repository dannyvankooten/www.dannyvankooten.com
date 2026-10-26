---
title: "CodeIgniter Sessions in IE7"
description: "Fixing CodeIgniter session issues in Internet Explorer 7 caused by underscores in the session cookie name."
date: "2011-01-31T00:00:00+00:00"
modified: "2026-05-14T15:54:23+02:00"
categories: ["Programming"]
---

Yesterday, while working on a fairly simple application that required a login functionality I came across a very strange problem with CodeIgniter and sessions in Internet Explorer 7. While testing everything seemed to work fine in all browsers, even in IE6 (I use IETester to test for the older versions of IE).

### CodeIgniter Sessions not working in IE7

When showing the application to some friends it turned out that they were not able to log in.

I thought that maybe their browser (Internet Explorer 7.) was blocking cookies so I asked them to lower their security setting. No luck.

After checking my code and some more testing I excluded my code from being the problem.

A few google queries later I found out that more CodeIgniter users have been experiencing the same problem with Internet Explorer 7 and CodeIgniter sessions not being stored.

### IE7 and the CI\_Session class

Since CodeIgniter sessions work fine in some IE7 browsers and fail in others there currently seems to be no definite fix for this problem.

#### Underscore in the CodeIgniter session cookie

One thing that at least seems related it is the underscore `_` in the cookie name that stores the session variables.

The default CI cookie name is ‘ci\_session’. Renaming that to `cisession`, without the underscore, did the trick for me.

```
$config['sess_cookie_name'] = 'cisession'; // note: no more underscore
```

#### Server time setting

If that didn’t do the trick for you you might try checking your server’s time settings. When cookies are being stored they are given a lifetime. It could be that your server’s time is a few hours behind and thus cookies will be removed immediately since their lifetime is already over.

#### Native PHP sessions

The last thing you can do is to drop the CodeIgniter session library and start using PHP’s default session handling using `session_start()`, `$_SESSION[]`, `unset` and `session_destroy`.

Have a look at the CodeIgniter Native Session Library if you really need to use a library to handle your sessions.
