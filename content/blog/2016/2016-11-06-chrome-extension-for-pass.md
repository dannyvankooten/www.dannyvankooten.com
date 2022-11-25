+++
title = "Browserpass, a Chrome extension for Pass"
date = 2016-11-06 15:30:00
+++

Last week I treated myself to some hardware upgrades for my desktop, which will be my main workstation from now on.
After installing Ubuntu Gnome, I was pleasantly surprised to find that most of my favorite applications from OSX have a Linux version.

One application that does not have a native Linux client is 1Password, my (now ex-) password manager. Luckily, there's [Pass](https://www.passwordstore.org/).

### Pass

Pass is a password manager for UNIX, where each password is stored in a GPG encrypted file. It provides commands for adding, editing, generating, and retrieving passwords.

I really like being able to type `pass -c website.com` to have my password decrypted & available on my clipboard for 45 seconds. Add in TAB completion and this allows me to retrieve passwords even faster than I was used to from my 1Password days.

Since every password is stored in a separately encrypted file, it's a lot easier to synchronise your passwords across multiple devices too.

One downside is that the filenames you're using for passwords may give away the existence of certain accounts. No big deal for me, but it might be for some.

### Browser extension for Pass

The majority of my passwords are for websites.

While tools like [guake (a drop down terminal)](http://guake-project.org/) allow me to access the command-line fast enough, I love shaving seconds off a repetitive task like logging in to yet another site.

A quick search for a Chrome extension for pass yielded no results, so I set off to build something myself.

### Browserpass. A Chrome extension for Pass using Golang.

The result is [browserpass](https://github.com/dannyvankooten/browserpass), a Chrome extension that uses native messaging to talk to a local binary written in Golang. The binary handles the interfacing between the browser extension and Pass in a secure way.

![Browserpass in action](/media/browserpass.gif)

When using Browserpass, your password store needs to have a directory structure of `DOMAIN/USERNAME.gpg` for the best results.

Hit <strong>Ctrl + M</strong> or click the lock icon to open up Browserpass. It will search for logins for the current domain by default, but the search field allows you to quickly grab any of your logins.

Cycle through the results using <strong>TAB</strong> and then hit <strong>ENTER</strong> to auto-fill & submit the login form.

### Installing the Chrome extension

Prebuilt binaries for OSX and Linux are available. Please see [the README file](https://github.com/dannyvankooten/chrome-gopass/blob/master/README.md) for detailed installation instructions.

_(Update Nov 28, 2016: [Browserpass is now available for Firefox as well](@/blog/2016/2016-11-28-browserpass-for-firefox.md))_
