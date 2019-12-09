[![Packagist](https://img.shields.io/packagist/v/pluswerk/typoscript-auto-fixer.svg?style=flat-square)](https://packagist.org/packages/pluswerk/typoscript-auto-fixer)
[![Packagist](https://img.shields.io/packagist/l/pluswerk/typoscript-auto-fixer.svg?style=flat-square)](https://opensource.org/licenses/LGPL-3.0)
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

## Configuration

The fixes are done based on the typoscript linter configuration. Only if a sniffer class is configured the
corresponding fixer is executed.

The configuration is the same as [martin-helmich/typo3-typoscript-lint](https://github.com/martin-helmich/typo3-typoscript-lint).

If grumphp is used the configuration is done as here: [pluswerk/grumphp-typoscript-task](https://github.com/pluswerk/grumphp-typoscript-task).

### Fixers exist for following sniffer classes

* EmptySection
* OperatorWhitespace
* Indentation
* NestingConsistency

For details see What is fixed section

## What is fixed

### Line breaks

Multiple empty lines are reduced to one empty line.

#### Example

```typo3_typoscript
foo.bar = value



another.foo = value2
```

__fixed:__

```typo3_typoscript
foo.bar = value

another.foo = value2
```

### Operator whitespaces (configuration class: OperatorWhitespace)

#### Example

```typo3_typoscript
foo=bar
```

__fixed:__

```typo3_typoscript
foo = bar
```

### Indentation (configuration class: Indentation)

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

### Empty section (configuration class: EmptySection)

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

### Nesting consistency (configuration class: NestingConsistency)

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
