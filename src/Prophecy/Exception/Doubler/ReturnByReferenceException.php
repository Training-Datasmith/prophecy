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

class Return_By_Reference_Exception extends Double_Exception
{
    /**
     * @param string $message
     * @param string $classname
     * @param string $methodName
     */
    public function __construct($message, private $classname, private $method_name)
    {
        parent::__construct($message);
    }
    /**
     * @return string
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
}