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
namespace Prophecy\Doubler\Class_Patch;

use Prophecy\Doubler\Generator\Node\Class_Node;
/**
 * Class patch interface.
 * Class patches extend doubles functionality or help
 * Prophecy to avoid some internal PHP bugs.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface Class_Patch_Interface
{
    /**
     * Checks if patch supports specific class node.
     *
     *
     * @return bool
     */
    public function supports(Class_Node $node);
    /**
     * Applies patch to the specific class node.
     *
     * @return void
     */
    public function apply(Class_Node $node);
    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function get_priority();
}