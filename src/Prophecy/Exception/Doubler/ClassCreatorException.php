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

use Prophecy\Doubler\Generator\Node\Class_Node;
class Class_Creator_Exception extends \RuntimeException implements Doubler_Exception
{
    /**
     * @param string    $message
     */
    public function __construct($message, private readonly Class_Node $node)
    {
        parent::__construct($message);
    }
    /**
     * @return ClassNode
     */
    public function get_class_node()
    {
        return $this->node;
    }
}