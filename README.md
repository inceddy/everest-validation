# Everest - Validation Component

The Validation Component of Everest is ment to validate user input in a simple and intuitive way.

## Installing

Simply go to your project directory where the `composer.json` file is located and type:

```sh
	composer require everest/validation
```

## Usage

This package offers two methods of data validation. You can either validate a single value on a specific requirement (type) or you can validate one or more validation chains on each key of an array or array like object.

### Validating single value

```php
use Everest\Validation\Validation;

$int = Validation::integer('10'); // $int -> 10
$noint = Validation::integer('foo'); // Will throw \Everest\Validation\InvalidValidationException
```

### Validating an array of values using a validation chain

```php
use Everest\Validation\Validate;

$data = [
	'foo' => '10',
	'bar' => 'foo'
];

$data = Validate::lazy($data)
	->that('foo')->integer()->between(0, 20)
	->that('bar')->enum(['bar', 'foo'])->upperCase()
	->execute();

// $data -> ['foo' => 10, 'bar' => 'FOO']
```

You can use additional validation chains by seperating them with `->or()`

```php
use Everest\Validation\Validate;

$data = [
	'bar' => 'foo'
];

$data = Validate::lazy($data)
	->that('bar')->integer()->between(0, 20)
	->or()->string()->minLength(2)
	->execute();

// $data -> ['bar' => 'FOO']
```

### Strict and lazy validation
One can choose between `Validate::strict()` and `Validate::lazy()`. First will throw an `InvalidValidationException` on the first occuring error and the last will collect all occuring errors and will throw a `InvalidLazyValidationException`, which provices a `::getErrors()` and `::getErrorsGroupedByKey()` method to access all bundled `InvalidValidationException` exceptions.

### Validating nested arrays
One can use dot-notation to validate nested values.

```php
use Everest\Validation\Validate;

$data = [
	'foo' => [
		['bar' => 1, 'name' => 'Some name'],
		['bar' => 2, 'name' => 'Some other name']
	]
];

$data = Validate::lazy($data)
	->that('foo.*.bar')->integer()->between(0, 20)
	->that('foo.*.name')->string()->upperCase()
	->execute();

// $data -> [
//   'foo' => [
//     ['bar' => 1, 'name' => 'SOME NAME'],
//     ['bar' => 2, 'name' => 'SOME OTHER NAME']
//   ]
// ]
```

### Optional parameters
Parameters can be marked as optional and as optional with default. If the validation ueses a default value as fallback this value is NOT validated by the validation chain anymore!

```php
use Everest\Validation\Validate;

$data = ['foo' => 10];

$result = Validate::lazy($data)
	->that('foo')->integer()
	->that('bar')->optional(/* no default */)->integer()
	->execute();

// $result -> ['foo' => 10]

$result = Validate::lazy($data)
	->that('foo')->integer()
	->that('bar')->optional(null)->integer()
	->execute();

// $result -> ['foo' => 10, 'bar' => null]
```

## Types
Types are rules that a supplied value has to fulfill.

#### Array
`Validation::array($value)`, validates that given value is an array.

#### Between
`Validation::between($value, numeric $min, numeric $max)`, validates that given value holds `$min <= $value <= $max`.

#### Boolean
`Validation::boolean($value)`, validates that given value is a boolean or booleanish value. The result will be casted to a boolean. This type inteprets `$value` as follows:

| Value   | Result |
|---------|--------|
| true    | true   |
| 'true'  | true   |
| 1       | true   |
| '1'     | true   |
| false   | false  |
| 'false' | false  |
| 0       | false  |
| '0'     | false  |

Every other value will throw an `InvalidValidationException`.

#### DateTime
`Validation::dateTime($value, string $pattern)`, validates that given value matches the supplied date pattern and returns a new `DateTime` instance.

#### DateTimeImmutable
Same as *DateTime* but returns a new `DateTimeImmutable` instance.

#### Enum
`Validation::dateTime($value, array $enum)`, validates that given value matches one of the `$enum` values. If `$enum` is an assoc array it tryes to match `$value` against the keys and returns the associated value.

#### Float
`Validation::float($value)`, validates that given value is numeric. The result will be casted to a float.

#### Integer
`Validation::integer($value)`, validates that given value is an integer or integerish. The result will be casted to an integer.

#### KeyExists
`Validation::keyExisits($value, $key)`, validates that given value is an array and that the supplied key exists in this array.

#### Length
`Validation::length($value, int $length)`, validates that given value matches the supplied string length using `strlen`.

#### LengthBetween
`Validation::lengthBetween($value, int $min, int $max)`, validates that given values string length is between supplied minimum and maximum.

#### LengthMax
`Validation::lengthMax($value, int $max)`, validates that given values string length lower or equal supplied maximum.

#### LengthMin
`Validation::lengthMin($value, int $min)`, validates that given values string length greater or equal supplied minimum.

#### Max
`Validation::max($value, int $max)`, validates that the given numerical value is lower or equal supplied maximum.

#### Min
`Validation::min($value, int $max)`, validates that the given numerical value is greater or equal supplied minimum.

#### NotEmpty
`Validation::notEmpty($value)`, validates that the given value is not empty.

#### Null
`Validation::null($value)`, validates that the given value is `null`.

#### String
`Validation::string($value)`, validates that the given value is a string.

## Filters
Filters can be used to transfrom the validated result.

#### LowerCase
`Validation::lowerCase($value)`, executes `strtolower` on the supplied value.

#### StripTags
`Validation::stripTags($value)`, executes `strip_tags` on the supplied value.

#### Trim
`Validation::trim($value)`, executes `trim` on the supplied value.

#### UpperCase
`Validation::upperCase($value)`, executes `strtoupper` on the supplied value.

### Custom Types

One can add custom types by creating a new class that extends from `Everest\Validation\Types\Type`.

```php
<?php

class CustomType extends \Everest\Validation\Types\Type {

	public static $errorName = 'invalid_custom_error';
	public static $errorMessage = '%s is not a valid custom type.';

	public function __invoke($value, $message = null, string $key = null, $customArg1 = null, $customArg2 = null)
	{
		if (/* Your invalid condition here */) {
			$message = sprintf(
				self::generateErrorMessage($message ?: self::$errorMessage),
				self::stringify($value)
			);

			throw new InvalidValidationException(self::$errorName, $message, $key, $value);
		}

		/**
		 * You may transform/cast the result before retuning it.
		 * In this case it is usefull to add a custom argument as 
		 * `$doCast` = false flag
		 */
		

		return $value;
	}
}

```

In the next step you need to connect your type with the `Everest\Validation\Validation` class.

```php
<?php

// Add as class. A singleton instance will be created when the type is requested the first time
\Everest\Validation\Validation::addType('custom_name', CustomType::CLASS);

// Add as instance. You can also supply a instance of your custom type. 
// E.g. when you need to do some configuration in `__construct()`
\Everest\Validation\Validation::addType('custom_name', new CustomType());
```

If you want to overload an existing type you need to pass `true` as third argument to `\Everest\Validation\Validation::addType`.

Now you can use the custom type by `Validation::custom_type($var)` or in a validation chain with `->custom_type()`.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details