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
namespace Prophecy\Promise;

use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
/**
 * Returns saved values one by one until last one, then continuously returns last value.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Return_Promise implements Promise_Interface
{
    /**
     * Initializes promise.
     *
     * @param array<mixed> $returnValues Array of values
     */
    public function __construct(private array $return_values)
    {
    }
    public function execute(array $args, Object_Prophecy $object, Method_Prophecy $method)
    {
        $value = array_shift($this->return_values);
        if (!count($this->return_values)) {
            $this->return_values[] = $value;
        }
        return $value;
    }
}