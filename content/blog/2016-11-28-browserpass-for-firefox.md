+++
title = "Browserpass for Firefox"
date = 2016-11-28 11:13:00
+++

Two weeks ago I announced Browserpass, a simple Chrome extension to access the [pass' passwordstore](https://www.passwordstore.org/) directly from your browser.

![Browserpass in action](/media/browserpass.gif)

After sharing my work on [/r/linux](https://www.reddit.com/r/linux/comments/5bhg0t/gopass_a_chrome_extension_for_pass/) and being generally well received, people started asking whether this could be ported to Firefox.

After doing some searching, this turned out to be much easier than expected due to the [Firefox WebExtensions API](https://developer.mozilla.org/en-US/Add-ons/WebExtensions) which landed in Firefox 50 (released last week).

> WebExtensions are a cross-browser system for developing browser add-ons. To a large extent the system is compatible with the extension API supported by Google Chrome and Opera. Extensions written for these browsers will in most cases run in Firefox or Microsoft Edge with just a few changes.

So, on my next "Google day" (days where I have to work with tech that is new for me) I set out to make it happen. The result is that Browserpass is now available for both Chrome & Firefox.

- [Browserpass on the Chrome Web Store](https://chrome.google.com/webstore/detail/browserpass/jegbgfamcgeocbfeebacnkociplhmfbk).
- [Browserpass on the Firefox Add-ons site](https://addons.mozilla.org/en-US/firefox/addon/browserpass/?src=ss)

### Installing Browserpass

Please note that both extensions still rely on the Browserpass binary to be installed on your system, otherwise the extension will be unable to talk to your password store.

![Browserpass installation](/media/browserpass-install-script.png)

Instructions for installing the binary can be found on the [Browserpass GitHub page](https://github.com/dannyvankooten/browserpass).

### Directory structure of your password store

In the initial version of Browserpass a simple but rather limiting directory structure was assumed. I've changed this so that you can now simply search for the name of your password file, regardless of the directory structure.

If the password file contains a line prefixed with either `login:` or `username:`, the extension will use that as the login name. Otherwise, the name of the password file will be used.
