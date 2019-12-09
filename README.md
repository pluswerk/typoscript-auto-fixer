[![Build Status](https://travis-ci.com/pluswerk/typoscript-auto-fixer.svg?branch=master)](https://travis-ci.com/pluswerk/typoscript-auto-fixer)
[![Coverage Status](https://img.shields.io/codecov/c/gh/pluswerk/typoscript-auto-fixer.svg?style=flat-square)](https://codecov.io/gh/pluswerk/typoscript-auto-fixer)
[![Quality Score](https://img.shields.io/scrutinizer/g/pluswerk/typoscript-auto-fixer.svg?style=flat-square)](https://scrutinizer-ci.com/g/pluswerk/typoscript-auto-fixer)

# typoscript-auto-fixer

This is an auto fixer for TYPO3 TypoScript code style based on
 [martin-helmich/typo3-typoscript-lint](https://github.com/martin-helmich/typo3-typoscript-lint) of Martin Helmich.

## Quick guide

### Composer

``composer require --dev pluswerk/typoscript-auto-fixer``

There is a default configuration, so no configuration must be given.

## What is fixed

### Line breaks

Multiple empty lines are reduced to one empty line.

### Operator whitespaces

#### Example

```typo3_typoscript
foo=bar
```

__fixed:__

```typo3_typoscript
foo = bar
```

### Indentation

Depending on configuration the indentation is fixed. Possible characters:

* spaces
* tabs

Also the amount of characters can be set. See [martin-helmich/typo3-typoscript-lint](https://github.com/martin-helmich/typo3-typoscript-lint) for details.

#### Example

* character: space
* indentPerLevel: 2

```typo3_typoscript
foo {
bar = value
}
```

__fixed:__

```typo3_typoscript
foo {
  bar = value
}
```

### Empty section

Empty sections are removed.

#### Example

```typo3_typoscript
bar = value

foo {
}

another = foo
```

__fixed:__

```typo3_typoscript
bar = value


another = foo
```

### Nesting consistency

Nesting consistency is built if paths can be merged in a file. Indentation is used like described above in indentation fixer.

#### Example

```typo3_typoscript
foo {
  bar = value
}

foo.bar2 = value2

foo {
  bar2 {
    nested = nested value
  }
}
```

__fixed:__

```typo3_typoscript
foo {
  bar = value
  bar2 = value2
  bar2 {
    nested = nested value
  }
}
```

## Usage


### Basic usage

```bash
./vendor/bin/tscsf [options] [file] [file] [...]
```

### Options

| Option                                | Description                                                  |
|---------------------------------------|--------------------------------------------------------------|
| -t, --typoscript-linter-configuration | Use typoscript-lint.yml file                                 |
| -g, --grumphp-configuration           | Use grumphp.yml file                                         |
| -c, --configuration-file              | For both options (-t, -g) a different file path can be given.|

#### Example

```bash
./vendor/bin/tscsf -g -c another-grumphp.yml some.typoscript other.typoscript
```
