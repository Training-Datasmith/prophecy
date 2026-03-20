<?php

declare (strict_types=1);
/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Prophecy;

use Prophecy\Call\Call_Center;
use Prophecy\Doubler\Cached_Doubler;
use Prophecy\Doubler\Class_Patch;
use Prophecy\Doubler\Doubler;
use Prophecy\Doubler\Lazy_Double;
use Prophecy\Exception\Doubler\Class_Not_Found_Exception;
use Prophecy\Exception\Prediction\Aggregate_Exception;
use Prophecy\Exception\Prediction\Prediction_Exception;
use Prophecy\Prophecy\Object_Prophecy;
use Prophecy\Prophecy\Revealer;
use Prophecy\Prophecy\Revealer_Interface;
use Prophecy\Util\String_Util;
/**
 * Prophet creates prophecies.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Prophet
{
    private readonly ?\Prophecy\Doubler\Doubler $doubler;
    private readonly \Prophecy\Prophecy\Revealer_Interface $revealer;
    private readonly \Prophecy\Util\String_Util $util;
    /**
     * @var list<ObjectProphecy<object>>
     */
    private array $prophecies = [];
    public function __construct(?Doubler $doubler = null, ?Revealer_Interface $revealer = null, ?String_Util $util = null)
    {
        if (null === $doubler) {
            $doubler = new Cached_Doubler();
            $doubler->register_class_patch(new Class_Patch\Spl_File_Info_Patch());
            $doubler->register_class_patch(new Class_Patch\Traversable_Patch());
            $doubler->register_class_patch(new Class_Patch\Throwable_Patch());
            $doubler->register_class_patch(new Class_Patch\Disable_Constructor_Patch());
            $doubler->register_class_patch(new Class_Patch\Prophecy_Subject_Patch());
            $doubler->register_class_patch(new Class_Patch\Reflection_Class_New_Instance_Patch());
            $doubler->register_class_patch(new Class_Patch\Magic_Call_Patch());
            $doubler->register_class_patch(new Class_Patch\Keyword_Patch());
        }
        $this->doubler = $doubler;
        $this->revealer = $revealer ?: new Revealer();
        $this->util = $util ?: new String_Util();
    }
    /**
     * Creates new object prophecy.
     *
     * @param null|string $classOrInterface Class or interface name
     *
     * @return ObjectProphecy
     *
     * @template T of object
     * @phpstan-param class-string<T>|null $classOrInterface
     * @phpstan-return ($classOrInterface is null ? ObjectProphecy<object> : ObjectProphecy<T>)
     */
    public function prophesize($class_or_interface = null)
    {
        $this->prophecies[] = $prophecy = new Object_Prophecy(new Lazy_Double($this->doubler), new Call_Center($this->util), $this->revealer);
        if ($class_or_interface) {
            if (class_exists($class_or_interface)) {
                return $prophecy->will_extend($class_or_interface);
            }
            if (interface_exists($class_or_interface)) {
                return $prophecy->will_implement($class_or_interface);
            }
            throw new Class_Not_Found_Exception(sprintf('Cannot prophesize class %s, because it cannot be found.', $class_or_interface), $class_or_interface);
        }
        return $prophecy;
    }
    /**
     * Returns all created object prophecies.
     *
     * @return list<ObjectProphecy<object>>
     */
    public function get_prophecies()
    {
        return $this->prophecies;
    }
    /**
     * Returns Doubler instance assigned to this Prophet.
     *
     * @return Doubler
     */
    public function get_doubler()
    {
        return $this->doubler;
    }
    /**
     * Checks all predictions defined by prophecies of this Prophet.
     *
     *
     * @throws Exception\Prediction\AggregateException If any prediction fails
     */
    public function check_predictions(): void
    {
        $exception = new Aggregate_Exception("Some predictions failed:\n");
        foreach ($this->prophecies as $prophecy) {
            try {
                $prophecy->check_prophecy_methods_predictions();
            } catch (Prediction_Exception $e) {
                $exception->append($e);
            }
        }
        if (count($exception->get_exceptions())) {
            throw $exception;
        }
    }
}