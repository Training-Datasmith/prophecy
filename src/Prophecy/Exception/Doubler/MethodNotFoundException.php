<?php

declare(strict_types=1);

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Exception\Doubler;

use Prophecy\Argument\ArgumentsWildcard;

class MethodNotFoundException extends DoubleException
{
    /**
     * @param string $message
     * @param string|object $classname
     * @param string $methodName
     * @param null|ArgumentsWildcard|array<mixed> $arguments
     */
    public function __construct($message, private $classname, private $methodName, private $arguments = null)
    {
        parent::__construct($message);
    }

    /**
     * @return object|string
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return null|ArgumentsWildcard|array<mixed>
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
