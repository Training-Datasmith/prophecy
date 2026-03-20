# Architecture: phpspec/prophecy

## Purpose

A highly opinionated test doubles library for PHP. Prophecy creates "prophetic" mock objects
by generating PHP classes at runtime. It distinguishes between "dummies", "stubs", and "spies"
through a fluent, BDD-style API focused on describing collaboration between objects.

## Directory Structure

```
src/Prophecy/
  Prophet.php          # Entry point: creates ObjectProphecy instances and checks predictions
  Prophecy/
    ObjectProphecy.php    # Represents a prophesied object; owns MethodProphecy instances
    MethodProphecy.php    # Represents a single method's expected behavior and predictions
    ProphecyInterface.php
    RevealedProphecy.php  # The actual mock object returned by reveal()
  Argument/
    ArgumentsWildcard.php     # Matches a call's argument list against tokens
    Token/                    # Argument token implementations:
      AnyValuesToken.php        # Matches any arguments (*)
      AnyValueToken.php         # Matches any single argument
      ExactValueToken.php       # Matches exact value (===)
      IdenticalValueToken.php   # Matches by identity
      TypeToken.php             # Matches by type (instanceof)
      CallbackToken.php         # Matches via custom callable
      ObjectStateToken.php      # Matches by object property state
      StringContainsToken.php   # Matches strings containing a substring
      ArrayEntryToken.php / ArrayCountToken.php / ...
  Call/
    CallCenter.php   # Records and retrieves actual method calls on the double
    Call.php         # Represents a single recorded call
  Comparator/
    ClosureComparator.php      # Compares closures (always equal to self)
    ProphecyComparator.php     # Compares ObjectProphecy to its revealed object
    Factory.php
  Doubler/
    Doubler.php               # Generates PHP class code for mock doubles
    CachedDoubler.php         # Caches generated classes to avoid re-generation
    NameGenerator.php         # Generates unique class names for doubles
    LazyDouble.php            # Lazily generates the double class on first reveal()
    Generator/
      ClassCodeGenerator.php  # Produces PHP code for the mock class
      ClassCreator.php        # eval()s the generated code and registers the class
      Node/                   # AST-like node representation of the generated class
    ClassPatch/               # Patches applied to generated class code:
      DisableConstructorPatch.php  # Suppresses __construct in the double
      MagicCallPatch.php           # Adds __call if not present
      ProphecySubjectPatch.php     # Injects ProphecySubjectInterface
      ThrowablePatch.php / SplFileInfoPatch.php / ...
  Exception/               # Domain exceptions (PredictionException, DoubleException, etc.)
  Prediction/
    CallPrediction.php         # shouldBeCalled() — must be called at least once
    CallTimesPrediction.php    # shouldBeCalledTimes(n) — exact call count
    NoCallsPrediction.php      # shouldNotBeCalled()
    CallbackPrediction.php     # Custom prediction via callable
    PredictionInterface.php
  Util/
    StringUtil.php   # Helpers for formatting error messages
```

## Key Design Decisions

### Code Generation via eval()

Prophecy generates PHP class source code as a string and `eval()`s it to create the
double class at runtime. The generated class implements `ProphecySubjectInterface` and
delegates all method calls to the `ProphecySubject` (which routes to `CallCenter`).

### Argument Token System

Arguments to prophesied methods are matched using a composable token system. Tokens
implement `ArgumentTokenInterface` with a `scoreArgument()` method returning a score
(false = no match, higher = better match). This allows flexible matching with different
specificity levels; the most specific matching prophecy wins.

### Separated Prediction from Stubbing

`MethodProphecy` separates "what the method returns" (stubbing) from "how it should be
called" (predictions). Predictions are checked lazily at `Prophet::checkPredictions()`,
which is called in `prophecy-phpunit`'s `#[PostCondition]`.

## Extension Points

- **Custom `ArgumentTokenInterface`** — add custom argument matchers.
- **Custom `ClassPatch`** — modify generated class code for special cases.
- **Custom `PredictionInterface`** — add custom call count/argument predictions.

## Dependency Flow

```
Prophet
  └─ ObjectProphecy (per class/interface to double)
       ├─ LazyDouble → Doubler → ClassCodeGenerator → eval()
       ├─ MethodProphecy[] (per method)
       │    ├─ ArgumentsWildcard → ArgumentTokenInterface[]
       │    ├─ WillImplementation (stub return value)
       │    └─ PredictionInterface[]
       └─ CallCenter (records actual calls on the revealed object)
```
