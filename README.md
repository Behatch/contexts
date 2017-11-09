Behatch contexts
================

[![Build status](https://travis-ci.org/Behatch/contexts.svg?branch=master)](https://travis-ci.org/Behatch/contexts)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Behatch/contexts/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Behatch/contexts/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Behatch/contexts/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Behatch/contexts/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ed08ea06-93c2-4b90-b65b-4364302b5108/mini.png)](https://insight.sensiolabs.com/projects/ed08ea06-93c2-4b90-b65b-4364302b5108)

Behatch contexts provide most common Behat tests.

Installation
------------

This extension requires:

* Behat 3+
* Mink
* Mink extension

### Project dependency

1. [Install Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
2. Require the package with Composer:

```
$ composer require --dev behatch/contexts
```

3. Activate extension by specifying its class in your `behat.yml`:

```yaml
# behat.yml
default:
    # ...
    extensions:
        Behatch\Extension: ~
```

### Project bootstraping

1. Download the Behatch skeleton with composer:

```
$ php composer.phar create-project behatch/skeleton
```

Browser, json, table and rest step need a mink configuration, see [Mink
extension](http://extensions.behat.org/mink/) for more information.

Usage
-----

In `behat.yml`, enable desired contexts:

```yaml
default:
    suites:
        default:
            contexts:
                - behatch:context:browser
                - behatch:context:debug
                - behatch:context:system
                - behatch:context:json
                - behatch:context:table
                - behatch:context:rest
                - behatch:context:xml
```

Configuration
-------------

* `browser` - more browser related steps (like mink)
    * `timeout` - default timeout
* `debug` - helper steps for debugging
    * `screenshotDir` - the directory where store screenshots
* `system` - shell related steps
    * `root` - the root directory of the filesystem
* `json` - JSON related steps
    * `evaluationMode` - javascript "foo.bar" or php "foo->bar"
* `table` - play with HTML the tables
* `rest` - send GET, POST, ... requests and test the HTTP headers
* `xml` - XML related steps

Translation
-----------

[![See more information on Transifex.com](https://www.transifex.com/projects/p/behatch-contexts/resource/enxliff/chart/image_png)](https://www.transifex.com/projects/p/behatch-contexts/)
