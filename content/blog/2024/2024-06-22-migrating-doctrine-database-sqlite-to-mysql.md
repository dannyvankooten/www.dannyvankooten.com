+++
title = "Migrating a Doctrine database from SQLite to MySQL"
+++

Recently I had to migrate a production database from SQLite[^1] to MySQL for a Symfony application using Doctrine. I assumed this would be as easy as running a single command, but it turned out to be slightly more involved. Not hard by any means, but I did have to figure out a few things and piece together a few pieces of the puzzle.

The first few searches lead me to SQLite's `.dump` command[^2], which dumps the entire database (including table structure and indices) to a text file. This approach requires you to manually edit out the differences between SQLite and MySQL in SQL syntax, but this seemed a bit tedious to me. Surely there must be a better way, especially when using Doctrine?

In the end I came up with a reasonably short bash script which succesfully creates the MySQL database structure and copies over all data from the SQLite database with minimal downtime. In this post I'll walk you through the script, so that you can hopefully modify and use it for your own needs.

### Setting up the bash script

First we'll create a new file, make sure it's executed using bash and set a few flags.

```
#!/usr/bin env bash

set -e
set -o pipefail
```

- `set -e`: The set -e option instructs bash to immediately exit if any command has a non-zero exit status.
- `set -o pipefail`: This setting prevents errors in a pipeline from being masked by later succesfull commands. If any command in a pipeline fails, that return code will be used as the return code of the whole pipeline.

### Connecting to the MySQL server

We want our application to keep using the SQLite database until we run this script. So we start out by writing our updated database connection string to the `.env.local` file.

```
echo 'DATABASE_URL="mysql://user:password@host/database_name"' >> .env.local
```

Note that your `DATABASE_URL` may require a `serverVersion` and `charset` argument[^4], but I left that out for simplicity reasons.

### Creating the new database

We can use the commands from the Doctrine Symfony bundle to create our (empty) database. This also ensures our database connection string is working before we proceed.

```sh
echo "-- Setting up new MySQL database"
bin/console doctrine:database:create
```

### Creating database tables and indices

Now, instead of converting our SQLite database structure to MySQL we can simply have Doctrine inspect our current PHP models ("entities") and create the schema from that.

```sh
echo "-- Creating table structure"
bin/console doctrine:schema:update --force
```

We should now have our database in MySQL with a bunch of empty tables in it. Let's make sure by running `doctrine:schema:validate`.

```sh
echo "-- Validating schema"
bin/console doctrine:schema:validate
```

A nice side effect of creating the database structure from the current PHP models is that we can remove any previously used Doctrine migrations after we're done migrating from SQLite to MySQL, effectively starting from a clean slate.


### Migrating the data

OK, now the important part. Copying over all data from SQLite to our new MySQL database.

SQLite has an option for changing output formats[^3], with one of them being `insert` mode. This output mode will output the textual result of any `SELECT` queries you do as `INSERT` queries. Insert mode can be used to generate text that can later be used to input data into a different database.

One caveat is that we have to manually specify our columns in the right order in our `SELECT` queries, as the order of columns in SQLite can differ from the order of attributes in our PHP models.

```sh
echo "-- Dumping current data"
rm -f var/data-dump.sql || true # Remove any leftovers from previous (attempted) runs
sqlite3 db.sqlite -cmd ".mode insert users" 'SELECT id, email, password, name FROM users;' >> var/data-dump.sql
sqlite3 db.sqlite -cmd ".mode insert payments" 'SELECT id, amount FROM payments;' >> var/data-dump.sql
# Repeat for all your database tables
```

We should now have a text file called `data-dump.sql` which contains our production data as `INSERT queries. We can simply execute this on our MySQL database and be done with it!

```sh
echo "-- Importing data"
mysql database_name < var/data-dump.sql
```

At this point there is just one thing left that needs to be done, and that is removing the old `DATABASE_URL` line from `.env.local` and confirming that everything went well.

Of course your process may look slightly different, but I hope this gives you some ideas on how to tackle this problem in a mostly automated way.

[^1]: Yes, I have no qualms running SQLite in production for some of my projects. [Why does SQLite in production have such a bad rep?](https://avi.im/blag/2024/sqlite-bad-rep/)
[^2]: https://sqlite.org/cli.html#converting_an_entire_database_to_a_text_file
[^3]: https://sqlite.org/cli.html#changing_output_formats
[^4]: https://symfony.com/doc/6.4/doctrine.html#configuring-the-database
