---
title: "Setting up a VPS for static site hosting"
description: "Setting up a cheap VPS with nginx for hosting static sites, as documentation for future reference."
date: "2024-02-20T00:00:00+00:00"
modified: "2026-05-14T20:03:26+02:00"
categories: ["Web Performance"]
---

Remember me moving this site over to sourcehut pages last week? It did not last long. That was not really because of sourcehut pages itself.

I had spent the previous few weeks moving some friends and family back to shared hosting, which turned out to be amazing value for money. For less than $10 per month, you get Apache, MySQL LTS, PHP 8.2, 64 MB of Varnish, SSH access, and hourly backups.

With me going back to full-time employment in a few weeks, I liked the idea of having fewer systems to maintain. So I migrated all my remaining PHP applications to that same shared hosting. With this site on sourcehut pages, I could then power down all of the virtual servers I was renting.

This worked well, but I missed one capability: an easy way to run code connected to the internet. I am especially interested in this because I am playing with the idea of allowing comments on this blog by letting people email a specific address.

I decided to spin up a cheap VPS again and use it to host my various static sites. `hut publish` is great, but so is `rsync -rav`. I’ll use this post as documentation for future me, and hopefully it is useful to others in a similar boat too.

### Server details

For this server, we do not need much. A single-core vCPU with 1 GB of RAM, IPv4 and IPv6 networking enabled, some storage, and Debian installed is plenty.

If your cloud provider lets you configure a firewall from their UI, allow inbound traffic only on ports 22 (SSH), 80 (HTTP), and 443 (HTTPS).

### Software

Once logged in, update the base packages and install what we need.

```
apt update
apt upgrade
apt install vim nginx certbot python3-certbot-nginx
```

We could get somewhat newer versions by adding the nginx APT repository and using snap to install Certbot, but I am sticking to the Debian packaged versions here.

### Configuring nginx

We’ll store our websites and configuration files in `/var/www/`.

Open `/etc/nginx/nginx.conf` and add the following line inside the `http { }` block:

```
include /var/www/nginx/*
```

This tells nginx to include all files in the `/var/www/nginx` directory, allowing us to leave the rest of this file alone.

Create that directory and, inside it, create a file called `nginx.conf` for global configuration across all sites.

```
mkdir /var/www/nginx
touch /var/www/nginx/nginx.conf
```

The first thing we want to do is disable the `server_tokens` directive so nginx stops including its version in HTTP headers.

```
/var/www/nginx/nginx.conf
```

```
server_tokens off;
```

Next, enable [gzip compression](http://nginx.org/en/docs/http/ngx_http_gzip_module.html) and configure it properly.

```
/var/www/nginx/nginx.conf
```

```
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_buffers 32 4k;
gzip_min_length 1024;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;
```

This enables gzip compression for HTML, CSS, SVG, and JS responses at a level that strikes a good balance between compute cost and compression ratio.

Responses with a `Content-Length` header below 1024 bytes are not compressed, since they would barely benefit from it.

To determine a good setting for `gzip_buffers`, use [getconf](https://man.openbsd.org/getconf.1) to get the size of a memory page on your system.

```
getconf PAGESIZE
```

If this returns a value other than 4096, modify the `gzip_buffers` setting accordingly.

### Serving your site

Next, create another file containing the server configuration for your static site.

```
/var/www/nginx/www.dannyvankooten.com
```

```
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

Test your configuration with `nginx -t`. If that succeeds, reload nginx with `nginx -s reload`.

### Uploading your site

This site uses [gozer](https://github.com/dannyvankooten/gozer) to turn Markdown into HTML files and generate an RSS feed. Uploading the site to our server is a simple matter of using rsync:

```
rsync -rav build/. remote-user@remote-host:/var/www/www.dannyvankooten.com
```

The nice thing about this is that on subsequent calls, only modified files are transferred. We could use `-z` to enable compression, but the gains are fairly minimal because files are sent in isolation.

### Update your DNS records

Our site is ready to go live. You can preview it by adding a temporary entry to your `/etc/hosts` file.

```
123.456.789.123 www.dannyvankooten.com dannyvankooten.com
```

If everything looks good, update the DNS records of your domain so it has an `A` and `AAAA` record pointing to your server.

You can verify the DNS change using `dig`:

```
dig dannyvankooten.com +noall +answer -t A
dig dannyvankooten.com +noall +answer -t AAAA
```

### Enable HTTPS

With your domain pointing to your server, it is time for the final step: enabling HTTPS on your site. We already have Certbot installed, so creating a new SSL certificate that automatically renews every 3 months is as easy as:

```
certbot --nginx -d dannyvankooten.com,www.dannyvankooten.com
```

That’s it. You can repeat the steps above for multiple sites. Unless your blog gets millions of pageviews per day, you can easily host a dozen static sites like this without making the server work hard.

### Tweaks

What follows are some final tweaks. They are not strictly necessary, but they are useful.

#### Creating a non-root user

It is a good idea to create a non-privileged user account that requires `sudo` for actions that need elevated permissions.

```
adduser danny
adduser danny sudo
adduser danny www-data
```

#### Only allow SSH access using public key authentication

First, add your public key to `$HOME/<user>/.ssh/authorized_keys`.

Then open `/etc/ssh/sshd_config` and disable password authentication.

```
/etc/ssh/sshd_config
```

```
PasswordAuthentication no
```

#### Increasing the soft limit for open file descriptors

nginx defaults to 768 worker connections. We [should increase our soft limit for open file descriptors](https://www.nginx.com/blog/avoiding-top-10-nginx-configuration-mistakes/#insufficient-fds) to at least twice that value.

Open `/etc/security/limits.conf` and add the following line just before the end of the file:

```
/etc/security/limits.conf
```

```
www-data soft nofile 1536
```

#### Disabling or buffering access logging

Logging every request consumes both CPU and I/O cycles. You can disable it entirely by including the following directive in your configuration file.

```
/var/www/nginx/nginx.conf
```

```
access_log off;
```

Another way to reduce the impact is to enable access log buffering.

```
/var/www/nginx/nginx.conf
```

```
access_log /var/log/nginx/access.log combined buffer=4096 flush=1m;
```

This writes to the log only once the 4 kB buffer is full or a minute has passed since the last write.

#### Limit request body size

Since we are hosting a static website that does not accept any request data, we can change the default value for `client_max_body_size` to something much closer to zero.

```
/var/www/nginx/nginx.conf
```

```
client_max_body_size 4k;
```

\[^1\]: My two reservations are that they seem to have been somewhat less reliable in terms of uptime since [they suffered a huge DDoS](https://sourcehut.org/blog/2024-01-19-outage-post-mortem/) a while ago, and that I am [unsure whether their new servers are powered by renewable energy](https://www.thegreenwebfoundation.org/green-web-check/?url=pages.sr.ht).

\[^2\]: You can of course pick other distributions. A lot of tutorials online use Ubuntu, but I really enjoy Debian (which Ubuntu is based on) and its stability.
