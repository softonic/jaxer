Jaxer
====================

[![Latest Version](https://img.shields.io/github/release/softonic/jaxer.svg?style=flat-square)](https://github.com/softonic/jaxer/releases)
[![Software License](https://img.shields.io/badge/license-Apache%202.0-blue.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/softonic/jaxer/master.svg?style=flat-square)](https://travis-ci.org/softonic/gjaxer)
[![Total Downloads](https://img.shields.io/packagist/dt/softonic/jaxer.svg?style=flat-square)](https://packagist.org/packages/softonic/jaxer)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/softonic/jaxer.svg?style=flat-square)](http://isitmaintained.com/project/softonic/jaxer "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/softonic/jaxer.svg?style=flat-square)](http://isitmaintained.com/project/softonic/jaxer "Percentage of issues still open")

**One rule engine to rule them all**

Jaxer allows you to evaluate complex rules in an easy way and know the reason behind the validation result.

Main features
-------------

* Evaluate rules.
* Rules evaluation tracing.
* Provide generic rules:
  * [Logical And](https://en.wikipedia.org/wiki/Truth_table#Logical_conjunction_(AND)) 
  * [Logical Or](https://en.wikipedia.org/wiki/Truth_table#Logical_disjunction_(OR))
  * [Logical Not](https://en.wikipedia.org/wiki/Truth_table#Logical_negation)

Installation
-------------

You can require the last version of the package using composer
```bash
composer require softonic/jaxer
```

### Usage

The rules are classes that needs to implement `\Softonic\Jaxer\Rules\Rule` interface. They usually get their input
parameters through constructor like for example:

```php
<?php

use \Softonic\Jaxer\Rules\Rule;

class IsNumberHigherThanFive implements Rule {
    /**
     * We receive the parameters from constructor.
     * You could also use specific setters or direct access to attribute.
     */
    public function __construct(private int $number)
    {
    }

    /**
     * Business logic to be evaluated.
     */
    public function isValid(): bool
    {
        return $this->number > 5;
    }
    
    /**
     * [Tracing] Context information to provide in case to know what happened in the evaluation.
     */
    public function getContext(): array
    {
        return [
            'number' => $this->number,
        ];
    }
}
```

As you can see there is a part dedicated to the business logic in the `isValid` method and a `getContext` method that 
allows you to track the rule information.

On the other hand, this library provide generic rules, which help to evaluate complex rules.
These logical operation are: [AND](./src/Rules/AndRule.php), [OR](./src/Rules/OrRule.php) and [NOT](./src/Rules/NotRule.php).

#### Example using AndRule, OrRule and NotRule
```php
<?php

use Softonic\Jaxer\Rules\AndRule;
use Softonic\Jaxer\Rules\NotRule;
use Softonic\Jaxer\Rules\OrRule;
use Softonic\Jaxer\Rules\Rule;
use Softonic\Jaxer\Validation;

class FalseRule implements Rule {
    public function isValid(): bool
    {
        return false;
    }

    public function getContext(): array
    {
        return [];
    }
}

class TrueRule implements Rule {
    public function isValid(): bool
    {
        return true;
    }

    public function getContext(): array
    {
        return [];
    }
}

$logicalRule = new OrRule( //true
    new AndRule( //false
        new TrueRule(),
        new FalseRule(),
        new TrueRule(),
    ),
    new AndRule( //true
        new NotRule( // true
            new FalseRule()
        ),
        new TrueRule()
    )
);

$validation = new Validation($logicalRule);
$validation->isValid(); // true
$validation->getContext(); // ['rule' => \Softonic\Jaxer\Rules\OrRule, 'context' => [...], 'evaluated' => true, 'isValid' => true]
```

After a validation is evaluated, you can execute `isValid` as many times you want **without performance impact** because it
is evaluated only the first time it is called.

### Advanced Usage

#### Rules with inner rules

Sometimes you need to create a rule that contain other rules,
like for example [AND](./src/Rules/AndRule.php), [OR](./src/Rules/OrRule.php) and [NOT](./src/Rules/NotRule.php).
Those rules contain other rules that need to be evaluated in validation time.

To evaluate a rule you need to encapsulate it in a `Validation` object. It will decorate the rule adding state and a common context format.

Logical Not Example:
```php
<?php

namespace Softonic\Jaxer\Rules;

use Softonic\Jaxer\Validation;

class NotRule implements Rule
{
    // Validation decorator
    private Validation $validation;

    public function __construct(private Rule $rule)
    {
        // Encapsulate rule inside a validation
        $this->validation = new Validation($rule);
    }

    public function isValid(): bool
    {
        // Validate the rule
        return !$this->validation->isValid();
    }

    public function getContext(): array
    {
        // Get the validation context
        return $this->validation->getContext();
    }
}
```

Testing
-------

`softonic/jaxer` has a [PHPUnit](https://phpunit.de) test suite, and a coding style compliance test suite using [PHP CS Fixer](http://cs.sensiolabs.org/).

To run the tests, run the following command from the project folder.

``` bash
$ make tests
```

To open a terminal in the dev environment:
``` bash
$ make debug
```

License
-------

The Apache 2.0 license. Please see [LICENSE](LICENSE) for more information.
