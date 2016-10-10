Behatch contexts
================

.. image:: https://travis-ci.org/Behatch/contexts.svg?branch=master
    :target: https://travis-ci.org/Behatch/contexts
    :alt: Build status

.. image:: https://scrutinizer-ci.com/g/Behatch/contexts/badges/quality-score.png?b=master
    :target: https://scrutinizer-ci.com/g/Behatch/contexts/?branch=master
    :alt: Scrutinizer Code Quality

.. image:: https://scrutinizer-ci.com/g/Behatch/contexts/badges/coverage.png?b=master
    :target: https://scrutinizer-ci.com/g/Behatch/contexts/?branch=master
    :alt: Code Coverage

.. image:: https://insight.sensiolabs.com/projects/ed08ea06-93c2-4b90-b65b-4364302b5108/mini.png
    :target: https://insight.sensiolabs.com/projects/ed08ea06-93c2-4b90-b65b-4364302b5108
    :alt: SensioLabsInsight

Behatch contexts provide most common Behat tests.

Installation
------------

This extension requires:

* Behat 3+
* Mink
* Mink extension

Project dependency
~~~~~~~~~~~~~~~~~~

1. `Install Composer <https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx>`_

2. Require the package with Composer:

.. code-block:: bash

    $ composer require --dev behatch/contexts

3. Activate extension by specifying its class in your ``behat.yml``:

.. code-block:: yaml

    # behat.yml
    default:
        # ...
        extensions:
            Behatch\Extension: ~

Project bootstraping
~~~~~~~~~~~~~~~~~~~~

1. Download the Behatch skeleton with composer:

.. code-block:: bash

    $ php composer.phar create-project behatch/skeleton

.. note::

    Browser, json, table and rest step need a mink configuration, see
    `Mink extension <http://extensions.behat.org/mink/>`_ for more information.

Usage
-----

In ``behat.yml``, enable desired contexts:

.. code-block:: yaml

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

Configuration
-------------

* ``browser`` - more browser related steps (like mink)
    * ``timeout`` - default timeout
* ``debug`` - helper steps for debugging
    * ``screenshotDir`` - the directory where store screenshots
* ``system`` - shell related steps
    * ``root`` - the root directory of the filesystem
* ``json`` - JSON related steps
    * ``evaluationMode`` - javascript "foo.bar" or php "foo->bar"
* ``table`` - play with HTML the tables
* ``rest`` - send GET, POST, … requests and test the HTTP headers
* ``xml`` - XML related steps

Translation
-----------

.. image:: https://www.transifex.com/projects/p/behatch-contexts/resource/enxliff/chart/image_png
    :target: https://www.transifex.com/projects/p/behatch-contexts/
    :alt: See more information on Transifex.com
