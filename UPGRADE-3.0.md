UPGRADE FROM 2.x to 3.0
=======================

* All classes have moved to `Sanpi\Behatch` namespace to `Behatch`.

* The contexts aliases start with `behatch:context:` instead of `behatch:`
  prefix.

* Fixed miss spelling methods:
    * `JsonContext::theJsonNodesShoudBeEqualTo` => `JsonContext::theJsonNodesShouldBeEqualTo`
    * `JsonContext::theJsonNodesShoudContain` => `JsonContext::theJsonNodesShouldContain`
    * `JsonContext::theJsonNodesShoudNotContain` => `JsonContext::theJsonNodesShouldNotContain`
