CIPS, a Continuous Integration PHP Server
=========================================

CIPS is a simple continuous integration server written in php mainly for
php projects. It is based on [Silex][1], a PHP micro framework.

CIPS supports projects hosted in a Subversion or Git repository.

Currently CIPS supports tests which return a non-zero exit code when tests do
not pass, checkstyle results (like PHP Codesniffer), code coverage in clover
format (as provided by PHPUnit) as well as a link to the project documentation.

CIPS works with PHP 5.3.2 or later.

## Installation

To install CIPS download the source and run

```shell
composer install
```

in the root folder of the source to get all dependencies through
[Composer][2]. Then configure a vhost for the 
web directory and set up your projects under ``config/projects.php``. A
sample file is available under ``config/projects.php_sample``.
You have to add a config file under ``config/config.php`` too, a sample is
available under ``config/config.php_sample``.

If you use [Composer][2] for managing your dependencies, CIPS automatically
installs and updates your dependencies when a project is build. To use this
feature, you have to set the path to your composer installation in the
``config/config.php`` file like in the example under
``config/config.php_sample``.

## Usage

CIPS runs on the command line. To list all available commands
go to the root directory of the application and type

```shell
./cips
```

You can build all your projects with

```shell
./cips build
```

or pass a project slug to build a single project.
To continuously build your projects you can either run the task as a cronjob
or execute it as a hook from your version control system.

### Tests

You can set a test command for each project in ``config/projects.php`` with the
setTestCommand('') function, for example:

```php
<?php

$your_project->setTestCommand('phpunit tests/');
```

CIPS supports test and code coverage reports in xml format (the testresults in
junit format and the code coverage report in clover format). The file with the
xml test report has to be located under 
``data/buils/your_project_slug/reports/testresult.xml``,
the xml-file with the code coverage report has to be located under
``data/buils/your_project_slug/reports/coverage.xml``.

You can generate the reports like in the following PHPUnit example:

```php
<?php

$your_project->setTestCommand('phpunit tests/ --log-junit "../reports/testresult.xml" --coverage-clover "../reports/coverage.xml"');
```

### Checkstyle

You can set a checkstyle task as a post build command in 
``config/projects.php``.
Currently CIPS only supports the checkstyle format. The resulting xml-file
has to be located under 
``data/buils/your_project_slug/reports/checkstyle.xml``.

You can generate the report like in the following PHP Codesniffer example:

```php
<?php

$your_project->setPostBuildCommands(array(
    'phpcs --report=checkstyle --report-file=../reports/checkstyle.xml src/',
));
```

Because the report file in the example is generated with a relative path, there
is a problem in cause the file does not exist. In this case it will not be
generated, therefore you have to create it manually before the first build.

### Project Documentation

You can set a link to the documentation of your project in
``config/projects.php``.

You can set the link like in the following example:

```php
<?php

$your_project->setDocumentationLink('http://link/to/your/documentation');
```
You can create your documentation as a post success build command in your project.


### Notifications

There is only notification by email available at the moment. It can be configured
in the ``config/projects.php`` like in the following example:

```php
<?php

$your_project->setNotifier(new Cips\Notifications\EmailNotification('email1@example.com, email2@example.com', 'from-email@example.com', false));
```

The first parameter is a comma-seperated list of recipients, the second argument
is the sender address of the email and the third parameter controls if the email
is only send after a failure in the tests (false) or after every build (true).

## License

CIPS is licensed under the MIT license.

[1]: http://silex-project.org
[2]: http://getcomposer.org/