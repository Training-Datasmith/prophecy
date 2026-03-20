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
namespace Prophecy\Exception\Doubler;

use Prophecy\Argument\Arguments_Wildcard;
class Method_Not_Found_Exception extends Double_Exception
{
    /**
     * @param string $message
     * @param string|object $classname
     * @param string $methodName
     * @param null|ArgumentsWildcard|array<mixed> $arguments
     */
    public function __construct($message, private $classname, private $method_name, private $arguments = null)
    {
        parent::__construct($message);
    }
    /**
     * @return object|string
     */
    public function get_classname()
    {
        return $this->classname;
    }
    /**
     * @return string
     */
    public function get_method_name()
    {
        return $this->method_name;
    }
    /**
     * @return null|ArgumentsWildcard|array<mixed>
     */
    public function get_arguments()
    {
        return $this->arguments;
    }
}