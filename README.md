# sage-cacheable-viewloaders

A sage package for pre-compiling acorn view loaders and thus making them cacheable.

## Installation

```sh
composer require generoi/sage-cacheable-viewloaders
```

## Usage

To compile loaders of all the views run the following command.

_NOTE that this needs to run after `view:cache` since that command clears the caches._

```
wp acorn viewloader:cache
```
