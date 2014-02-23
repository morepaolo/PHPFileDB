PHPFileDB
=========

PHPFileDB is the temporary code name of this project, which implements a COMPLETE database written entirely in PHP.
It's an ongoing project, whose intent is to offer a valid embeddable database where sqlite is not available (let's think at those hosters outside which disable sqlite extensions in order to force users to buy their cheap mysql databases).

This project doesn't aim at replacing sqlite, as we know that we cannot reach the same performances with a library written entirely in PHP. The most interesting characteristic of PHPFileDB will be its complete compatibility with MySQL based websites, as we know how tedious can be to rewrite queries to support different databases. Obviously, this kind of compatibility can be reached only by using a third party database abstraction layer, such as ADODB PHP. So, everything will work fine unless you have written explicit calls to mysql_* functions in your code.

USAGE
=====

The file index.php shows how to use the database. Just copy the whole package in your webroot, give write permissions to temp and data directories, and open index.php

The project is not yet ready for usage in the wild, and is made available only for test and collaboration purposes.
SQL language and semantic is found in the phpfdb/PHPSqlParser directory. The parser is built using Wez Furlong's JLexPHP and lemon-php projects, which can be found at https://github.com/wez/lemon-php
lemon-php is an implementation of the lemon parser, which happens to be SQLite parser generator, capable to output PHP code instead of C++.

COLLABORATION
=============

When the project will be ready for public usage, we plan to create an official website, with a wiki containing development infos, and a trac endpoint to keep track of issues and bugfixes.
If you want to collaborate to the project there are many interesting things you can do: main project requires many hours of work, and the project will be ready in less time if we work together. SHOW CONTRIBUTORS query will show, in a table, Name, Location and optional Comments for each contributor of the project (almost like MySql, but with different names!!)

Here's a list of suggestions on how to contribute to the project:

* Development of the main project, which can be divided in a number of activities: implementation of MySql Semantic, Query Plans Optimizer, bug fixes and testing

* Testing framework, which executes the same queries against PHPFileDB and MySQL, and checks the results to find differences

* Testing of the database with the most common open source platforms available, like Joomla, Wordpress and Magento

* Find out a better name (and maybe a logo) for the project

If you have any problem or suggestions, please don't esitate contacting me at morepaolo@gmail.com. In order for me to organize emails, please write [phpfiledb] in the subject of the messages.

Thank you!!
