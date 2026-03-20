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
namespace Prophecy\Doubler\Generator;

use Prophecy\Exception\Doubler\Class_Creator_Exception;
/**
 * Class creator.
 * Creates specific class in current environment.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Class_Creator
{
    private readonly \Prophecy\Doubler\Generator\Class_Code_Generator $generator;
    public function __construct(?Class_Code_Generator $generator = null)
    {
        $this->generator = $generator ?: new Class_Code_Generator();
    }
    /**
     * Creates class.
     *
     * @param string         $classname
     *
     * @return mixed
     * @throws \Prophecy\Exception\Doubler\ClassCreatorException
     */
    public function create($classname, Node\Class_Node $class)
    {
        $code = $this->generator->generate($classname, $class);
        $return = eval($code);
        if (!class_exists($classname, false)) {
            if (count($class->get_interfaces())) {
                throw new Class_Creator_Exception(sprintf('Could not double `%s` and implement interfaces: [%s].', $class->get_parent_class(), implode(', ', $class->get_interfaces())), $class);
            }
            throw new Class_Creator_Exception(sprintf('Could not double `%s`.', $class->get_parent_class()), $class);
        }
        return $return;
    }
}