# Contributing

Behatch is an open source, community-driven project. If you'd like to
contribute, feel free to do this, but remember to follow this few simple rules:

* Make your feature addition or bug fix,
* __Always__ as base for your changes use `master` branch (all new development
  happens here),
* Add `*.features` for those changes (please look into `features/` folder for
  some examples). This is important so we don't break it in a future version
  unintentionally,
* __Remember__: when you create Pull Request, always select `master` branch as
  target, otherwise it will be closed (this is selected by default).

# Contributing to Formatter Translations

Almost step provide by Behatch could be translated into your language with
`--lang` option. In order to fix/add translation, edit the appropriate file in
the `i18n` directory.

# Running tests

Make sure that you don't break anything with your changes by running the test
suites:

```bash
$> ./bin/atoum
$> ./bin/behat
```
