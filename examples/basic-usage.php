<?php

declare(strict_types=1);

/**
 * Example: Using Prophecy for test doubles.
 *
 * Install:
 *   composer require --dev phpspec/prophecy
 *
 * With PHPUnit, use prophecy-phpunit for automatic verification:
 *   composer require --dev phpspec/prophecy-phpunit
 */

use Prophecy\Prophet;
use Prophecy\Argument;

// --- Setup ---

$prophet = new Prophet();


// --- Stub: define what a method returns ---

interface UserRepository
{
    public function find(int $id): ?array;
    public function save(array $user): bool;
}

$repo = $prophet->prophesize(UserRepository::class);

// Stub find() to return a specific user
$repo->find(1)->willReturn(['id' => 1, 'name' => 'Alice']);
$repo->find(Argument::type('int'))->willReturn(null); // default for any other int

// Get the actual mock object
$mock = $repo->reveal();

$user = $mock->find(1);    // ['id' => 1, 'name' => 'Alice']
$other = $mock->find(99);  // null


// --- Spy: assert a method was called ---

$repo->save(Argument::any())->shouldBeCalled();
$mock->save(['id' => 2, 'name' => 'Bob']);

$prophet->checkPredictions(); // throws PredictionException if not satisfied


// --- More specific argument matching ---

$logger = $prophet->prophesize(\Psr\Log\LoggerInterface::class);

// shouldBeCalledTimes asserts exact call count
$logger->info(Argument::containingString('user'))->shouldBeCalledTimes(1);
$logger->info('Created user successfully');

$prophet->checkPredictions();


// --- With PHPUnit + prophecy-phpunit trait (recommended) ---
//
// use PHPUnit\Framework\TestCase;
// use Prophecy\PhpUnit\ProphecyTrait;
//
// class UserServiceTest extends TestCase
// {
//     use ProphecyTrait;
//
//     public function testCreate(): void
//     {
//         $repo = $this->prophesize(UserRepository::class);
//         $repo->save(Argument::any())->willReturn(true)->shouldBeCalled();
//
//         $service = new UserService($repo->reveal());
//         $service->create(['name' => 'Alice']);
//         // prophecy predictions verified automatically after the test
//     }
// }
