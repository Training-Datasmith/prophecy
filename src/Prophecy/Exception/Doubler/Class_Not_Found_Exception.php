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

class Class_Not_Found_Exception extends Double_Exception
{
    /**
     * @param string $message
     * @param string $classname
     */
    public function __construct($message, private $classname)
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
}