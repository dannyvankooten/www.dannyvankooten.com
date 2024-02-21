+++
title = "Setting up a VPS for static site hosting"
+++

Remember me moving this site over to [sourcehut pages](https://srht.site/) last
week? It didn't last long. It doesn't have much to do with the service though[^1].

It's just that I used the last few weeks to move some friends and family back on
shared hosting, which turned out to be amazing value for money. You get Apache +
MySQL LTS + PHP 8.2 + 64 MB of Varnish for less than $10 per month, including
SSH access and hourly back-ups.

With me going back to full-time employment in a few weeks, I really liked the
idea of fewer responsibilities. So I migrated all my remaining PHP
applications to that same shared hosting. With this site on sourcehut pages, I
could then power down all of the virtual servers I was renting.

All was great for a couple of days, except for one thing... I missed having an
easy option to run code connected to the internet. Especially since I am playing
with the idea of allowing comments on this blog by allowing people to email to a
specific address.

I decided to spin-up a cheap VPS again and use it to host my various static
sites. `hut publish` is great, but so is `rsync -rav`. I'll use this post as
documentation for future me, but hopefully it's of use to others in a similar
boat too.

### Server details

For the server we don't need much; a single core vCPU with 1 GB of RAM, IPv4 and
IPv6 networking enabled, a bit of storage and Debian[^2] installed is plenty.

If your cloud provider has an option to configure a firewall from their UI,
configure it to only allow inbound traffic on port 22 (SSH), 80 (HTTP) and 443 (HTTPS).

### Software

Once logged in, we'll update the base packages and add what we need.

```sh
apt update
apt upgrade
apt install vim nginx certbot python3-certbot-nginx
```

We could get somewhat newer versions by adding the nginx APT repository and
using snap to install certbot, but
I am going to be sticking to Debian packaged versions here.

### Configuring nginx

We'll be storing our websites and configuration files in `/var/www/`.

Open up `/etc/nginx/nginx.conf` and add the following line inside the `http { }`
block:

```
include /var/www/nginx/*
```

This instructs nginx to include all files in the `/var/www/nginx` directory, allowing us to leave the rest of this file alone.

Create said directory and in it, create a file called `nginx.conf` that will
contain our global configuration (across all sites).

```
mkdir /var/www/nginx
touch /var/www/nginx/nginx.conf
```

The first thing we want to do is to disable the `server_tokens` directive, to
stop including the nginx version in HTTP headers.

```
server_tokens off;
```

Next up is enabling [gzip
compression](http://nginx.org/en/docs/http/ngx_http_gzip_module.html) and
configuring it properly.

```
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_buffers 32 4k;
gzip_min_length 1024;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;
```

This enables gzip compression for HTML, CSS, SVG and JS responses at a level
that strikes a nice balance between compute cost and compression ratio.

Responses with a `Content-Length` header of less than 1024 bytes are not
compressed, since they would barely benefit from it.

To determine a good setting for `gzip_buffers`, use
[getconf](https://man.openbsd.org/getconf.1) to get the
size of a memory page on your system.

```sh
getconf PAGESIZE
```

If this returns a value other than 4096, modify the `gzip_buffers` setting
accordingly.

### Serving your site

Next up, create another file containing the server configuration for your static
site.

File: `/var/www/nginx/www.dannyvankooten.com`
```sh
server {
    listen 80;
    listen [::]:80;
    index index.html;
    server_name www.dannyvankooten.com dannyvankooten.com;
    root /var/www/www.dannyvankooten.com;

    # Cache static assets for 1 year
    location ~* .(?:css|js|ico|txt|svg|jpg|jpeg|webp|png|csv)$ {
        expires 1y;
        add_header "Cache-Control" "public";
    }

    location / {
        try_files $uri $uri/ =404;
    }
}
```

Test your configuration with `nginx -t`. If that succeeds, reload nginx with
`nginx -s reload`.

### Uploading your site

This site uses [gozer](https://github.com/dannyvankooten/gozer) to turn Markdown into HTML files and generate an RSS
feed. Uploading the site to our server is a simple case of rsync:

```
rsync -rav build/. remote-user@remote-host:/var/www/www.dannyvankooten.com
```

The nice thing about this is that on subsequent calls, only modified files are
transferred. We could use `-z` to enable compression, but the gains are fairly
minimal because files are sent in isolation.

### Update your DNS records

Our site is ready to go live. You can preview it by adding a temporary entry in your
`/etc/hosts` file.

```sh
123.456.789.123 www.dannyvankooten.com dannyvankooten.com
```

If everything looks good, update the DNS records of your
domain so it has an `A` and `AAAA` record pointing to your server.

You can verify the DNS change using `dig`:

```sh
dig dannyvankooten.com +noall +answer -t A
dig dannyvankooten.com +noall +answer -t AAAA
```

### Enable HTTPS

With your domain pointing to your server, it is now time for the final step:
enabling HTTPS on your site. We already have Certbot installed, so creating a
new SSL certificate that automatically renews every 3 months is as easy as:

```sh
certbot --nginx -d dannyvankooten.com,www.dannyvankooten.com
```

That's it! You can repeat the steps above for multiple sites. Unless your blog
is getting millions of pageviews per dag, you can easily host a dozen of static
sites like this without your server really having to work.

### Tweaks

What follows are some final tweaks, Not strictly necessary but
nice nonetheless:

#### Creating a non-root user

It's always a good idea to create a non-privileged user account that requires
`sudo` to perform actions that require elevated permissions.

```
adduser danny
adduser danny sudo
adduser danny www-data
```


#### Only allow SSH access using PubKeyAuthentication

First, make sure to add your public key to `$HOME/<user>/.ssh/authorized_keys`.

Then open up `/etc/ssh/sshd_config` and disable password authentication.

```
PasswordAuthentication no
```

#### Increasing soft limit for open file descriptors

nginx defaults to 768 worker_connections. We [should increase our soft limit for
open file
descriptors](https://www.nginx.com/blog/avoiding-top-10-nginx-configuration-mistakes/#insufficient-fds) to at least twice that value.

Open up `/etc/security/limits.conf` and add the following line just before the
end of the file:

```
soft nofile 1536
```

#### Disabling or buffering access logging

Logging every request consumes both CPU and I/O cycles. You can disable it
entirely by including the following directive in your configuration file.

```
access_log off;
```

Another way to reduce the impact is to enable access log buffering.

```
access_log /var/log/nginx/access.log combined buffer=4096 flush=1m;
```

This will only write to the log once the 4 kB buffer is full or if a minute has
passed since the last write.

[^1]: If I had to nitpick two things it is that they do seem to be somewhat less
    reliable in terms of uptime since [they suffered a huge
    DDOS](https://sourcehut.org/blog/2024-01-19-outage-post-mortem/) a while ago.
    Also, I am [unsure whether their (new) servers are powered by renewable
    energy](https://www.thegreenwebfoundation.org/green-web-check/?url=pages.sr.ht).

[^2]: You can of course pick other distributions. A lot of tutorials online are
    using Ubuntu, but I really enjoy Debian (which Ubuntu is based on) and its
    stability.

