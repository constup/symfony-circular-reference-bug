# Symfony 5.4 "Circular reference" bug PHP 7.4

## Description

This repository demonstrates a "Circular reference detected for service" bug in Symfony 5.4 running on PHP 7.4.

## Installation

This is a default Symfony web application. Just clone the repository and run `composer install`.

## How to use this repository

### Branches

There are 5 branches in this repository:

- `master`: Blank Symfony 5.4 Web Application project. No code is added to this branch.
- `bugged`: This branch contains some sample code to demonstrate the bug.
- `not-bugged-and-workaround-1`: This branch demonstrates that the bug can be resolved simply by adding another object
which implements one of the interfaces.
- `not-bugged-and-workaround-2`: Another way to resolve the issue.
- `expected`: An example of expected behavior.

### Files

All sample files are in the `App\Example` namespace (`./src/Example` directory).

### How to trigger the bug

Checkout the `bugged` branch and run some Symfony console action. Example:

```bash
git checkout bugged
php bin/console list
```

You should get the following error:

```
Circular reference detected for service "App\Example\Circle", path: "App\Example\Circle -> App\Example\Circle". 
```

This is the dump of the `\Exception` object:

```
[9]{}Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException Object
(
    [serviceId:Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException:private] => App\Example\Circle
    [path:Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException:private] => Array
        (
            [0] => App\Example\Circle
            [1] => App\Example\Circle
        )

    [message:protected] => Circular reference detected for service "App\Example\Circle", path: "App\Example\Circle -> App\Example\Circle".
    [string:Exception:private] => 
    [code:protected] => 0
    [file:protected] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\dependency-injection\Compiler\CheckCircularReferencesPass.php
    [line:protected] => 67
    [trace:Exception:private] => Array
        (
            [0] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\dependency-injection\Compiler\CheckCircularReferencesPass.php
                    [line] => 70
                    [function] => checkOutEdges
                    [class] => Symfony\Component\DependencyInjection\Compiler\CheckCircularReferencesPass
                    [type] => ->
                )

            [1] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\dependency-injection\Compiler\CheckCircularReferencesPass.php
                    [line] => 43
                    [function] => checkOutEdges
                    [class] => Symfony\Component\DependencyInjection\Compiler\CheckCircularReferencesPass
                    [type] => ->
                )

            [2] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\dependency-injection\Compiler\Compiler.php
                    [line] => 82
                    [function] => process
                    [class] => Symfony\Component\DependencyInjection\Compiler\CheckCircularReferencesPass
                    [type] => ->
                )

            [3] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\dependency-injection\ContainerBuilder.php
                    [line] => 757
                    [function] => compile
                    [class] => Symfony\Component\DependencyInjection\Compiler\Compiler
                    [type] => ->
                )

            [4] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\http-kernel\Kernel.php
                    [line] => 548
                    [function] => compile
                    [class] => Symfony\Component\DependencyInjection\ContainerBuilder
                    [type] => ->
                )

            [5] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\http-kernel\Kernel.php
                    [line] => 789
                    [function] => initializeContainer
                    [class] => Symfony\Component\HttpKernel\Kernel
                    [type] => ->
                )

            [6] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\http-kernel\Kernel.php
                    [line] => 128
                    [function] => preBoot
                    [class] => Symfony\Component\HttpKernel\Kernel
                    [type] => ->
                )

            [7] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\framework-bundle\Console\Application.php
                    [line] => 168
                    [function] => boot
                    [class] => Symfony\Component\HttpKernel\Kernel
                    [type] => ->
                )

            [8] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\framework-bundle\Console\Application.php
                    [line] => 74
                    [function] => registerCommands
                    [class] => Symfony\Bundle\FrameworkBundle\Console\Application
                    [type] => ->
                )

            [9] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\console\Application.php
                    [line] => 171
                    [function] => doRun
                    [class] => Symfony\Bundle\FrameworkBundle\Console\Application
                    [type] => ->
                )

            [10] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\symfony\runtime\Runner\Symfony\ConsoleApplicationRunner.php
                    [line] => 54
                    [function] => run
                    [class] => Symfony\Component\Console\Application
                    [type] => ->
                )

            [11] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\vendor\autoload_runtime.php
                    [line] => 35
                    [function] => run
                    [class] => Symfony\Component\Runtime\Runner\Symfony\ConsoleApplicationRunner
                    [type] => ->
                )

            [12] => Array
                (
                    [file] => W:\bug_reports\symfony-circular-reference-bug\bin\console
                    [line] => 11
                    [args] => Array
                        (
                            [0] => W:\bug_reports\symfony-circular-reference-bug\vendor\autoload_runtime.php
                        )

                    [function] => require_once
                )

        )

    [previous:Exception:private] => 
)
```

## The cause of the bug

In the bugged example, class `Circle` implements two interfaces: `MathShapeInterface` and `TwoDimensionsInterface`. It
also has a property called `neighborOject` of type `MathShapeInterface`.

Since there is no other object which implements both of the interfaces, the compiler will try to wire up both interfaces 
with the `Circle` class. If an object has a property whose type is one of the interfaces, the `Circle` class will then
effectively point to itself (because of the typehint).

### Workaround No.1 (`not-bugged-and-workaround-1` branch)

By adding the `Square` object which implements the same interfaces as teh `Circle` object, the interfaces are no longer
being autowired to a specific class and the compiler will run successfully.

### Workaround No.2 (`not-bugged-and-workaround-2` branch)

By adding the `Cube` class, which implements `MathShapeInterface`, this interface is no longer autowired to a 
specific class. `TwoDimensionsInterface` will stay wired to the `Circle` class, but it doesn't matter, since 
`neighborObject`'s type is `MathShapeInterface`. This is why "Circular reference" bug will not be thrown.

## Expected behavior

When a class is implementing two interfaces, with one of them being the parent of the other, only the last one should be
autowired to point to our class.

In the example (on the `bugged` branch), `TwoDimensionsInterface` is extending `MathShapeInterface`. PHP does not allow
classes to implement interfaces which are already implemented, so the header of the class must be:

```php
class Circle implements MathShapeInterface, TwoDimensionsInterface
```

PHP will throw an exception if it's the other way around:

```php
class Circle implements TwoDimensionsInterface, MathShapeInterface
```

This is why the second interface in our example is considered to be the defining interface of the class. The first 
interface is merely there for better classification purposes and to help with abstraction between different class types.

The `expected` branch of this repository contains an example of how the dependency injection compiler should behave. In
the example on the `expected` branch, the property type of the `neighborobject` is changed to a new `SampleInterface`.
There are no classes which implement the `SampleInterface`, but the compiler run is still successful. Thus, the expected
behavior for processing `MathShapeInterface` on the `bugged` branch should be the same as for `SampleInterface` in the
`expected` branch - the `MathShapeInterface` should not be autowired to the `Circle` class if a child interface of 
`MathShapeInterface` is present and the `Circle` class is implementing the child interface.
