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

Through PHAR
~~~~~~~~~~~~

Download the .phar archives:

* `behat.phar <http://behat.org/downloads/behat.phar>`_ - Behat itself
* `mink.phar <http://behat.org/downloads/mink.phar>`_ - Mink framework
* `mink_extension.phar <http://behat.org/downloads/mink_extension.phar>`_ - Mink integration extension
* `behatch_contexts.phar <http://behat.org/downloads/behatch_contexts.phar>`_ - Behatch contexts

And activate it in your in your ``behat.yml``:

.. code-block:: yaml

    # behat.yml
    default:
        # ...
        extensions:
            behatch_contexts.phar: ~

Through Composer
~~~~~~~~~~~~~~~~

The easiest way to keep your suite updated is to use
`Composer <http://getcomposer.org>`_.

You can add Behatch contexts as dependencies for your project or rapidly
bootstrap a Behatch projects.

Project dependency
******************

1. Define dependencies in your ``composer.json``:

.. code-block:: js

    {
        "require-dev": {
            ...

            "behatch/contexts": "*"
        }
    }

2. Install/update your vendors:

.. code-block:: bash

    $ curl http://getcomposer.org/installer | php
    $ php composer.phar install

3. Activate extension by specifying its class in your ``behat.yml``:

.. code-block:: yaml

    # behat.yml
    default:
        # ...
        extensions:
            Sanpi\Behatch\Extension: ~

Project boostraping
*******************

1. Download the Behatch skeleton with composer:

.. code-block:: bash

    $ curl http://getcomposer.org/installer | php
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
                    - behatch:browser
                    - behatch:debug
                    - behatch:system
                    - behatch:json
                    - behatch:table
                    - behatch:rest
                    - behatch:xml

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
