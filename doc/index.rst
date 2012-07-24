Behatch contexts
================

Behatch contexts provide most common behat tests.

Installation
------------

This extension requires:

* Behat 2.4+
* Mink 1.4+

Through PHAR
~~~~~~~~~~~~

Download the .phar archives:

* `behat.phar <http://behat.org/downloads/behat.phar>` - Behat itself
* `mink.phar <http://behat.org/downloads/mink.phar>` - Mink framework
* `behatch_contexts.phar <http://behat.org/downloads/behatch_contexts.phar>` -
Behatch contexts

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
`Composer <http://getcomposer.org>`.

You can add behatch contexts as dependancies for your project or rapidly
bootstrap a behatch projects.

Project dependancy
******************

1. Define dependencies in your ``composer.json``:

.. code-block:: js

    {
        "require": {
            ...

            "sanpi/behatch-contexts": "*"
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

.. code-block:: bash

    $ curl http://getcomposer.org/installer | php
    $ php composer.phar create-project sanpi/behatch-skeleton

.. note::
    Browser, json, table and rest step need a mink configuration, see
    `Mink extension <http://extensions.behat.org/mink/>` for more informations.

Usage
-----

In your main context, using behatch contexts:

.. code-block:: php

    <?php

    use Behat\Behat\Context\BehatContext;
    use Sanpi\Behatch\Context\BehatchContext;

    class FeatureContext extends BehatContext
    {
        public function __construct(array $parameters)
        {
            $this->useContext('behatch', new BehatchContext($parameters));
        }
    }

After this, you wouldn't have new available step. You should enable,
in ``behat.yml``, the desired steps group:

.. code-block:: yml

    Sanpi\Behatch\Extension:
        browser: ~
        debug: ~
        system: ~
        json: ~
        table: ~
        rest: ~

Configuration
-------------

* ``browser`` - more browser related steps (like mink)
* ``debug`` - helper steps for debuging
    * ``screenshot_dir`` - the directory where store screenshots
    * ``screen_id`` - then xorg screen id
* ``system`` - shell related steps
    * ``root`` - the root directory of the filesystem
* ``json`` - JSON related steps
    * ``evaluation_mode`` - javascript "foo.bar" or php "foo->bar"
* ``table`` - play with HTML the tables
* ``rest`` - send GET, POST, … requests and test the HTTP headers
