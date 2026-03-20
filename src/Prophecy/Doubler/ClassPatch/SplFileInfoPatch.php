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
use Prophecy\Doubler\Generator\Node\Method_Node;
/**
 * SplFileInfo patch.
 * Makes SplFileInfo and derivative classes usable with Prophecy.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Spl_File_Info_Patch implements Class_Patch_Interface
{
    /**
     * Supports everything that extends SplFileInfo.
     *
     *
     */
    public function supports(Class_Node $node): bool
    {
        if ('SplFileInfo' === $node->get_parent_class()) {
            return true;
        }
        return is_subclass_of($node->get_parent_class(), 'SplFileInfo');
    }
    /**
     * Updated constructor code to call parent one with dummy file argument.
     */
    public function apply(Class_Node $node): void
    {
        if ($node->has_method('__construct')) {
            $constructor = $node->get_method('__construct');
            \assert($constructor !== null);
        } else {
            $constructor = new Method_Node('__construct');
            $node->add_method($constructor);
        }
        if ($this->node_is_directory_iterator($node)) {
            $constructor->set_code('return parent::__construct("' . __DIR__ . '");');
            return;
        }
        if ($this->node_is_spl_file_object($node)) {
            $file_path = str_replace('\\', '\\\\', __FILE__);
            $constructor->set_code('return parent::__construct("' . $file_path . '");');
            return;
        }
        if ($this->node_is_symfony_spl_file_info($node)) {
            $file_path = str_replace('\\', '\\\\', __FILE__);
            $constructor->set_code('return parent::__construct("' . $file_path . '", "", "");');
            return;
        }
        $constructor->use_parent_code();
    }
    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function get_priority(): int
    {
        return 50;
    }
    private function node_is_directory_iterator(Class_Node $node): bool
    {
        $parent = $node->get_parent_class();
        return 'DirectoryIterator' === $parent || is_subclass_of($parent, 'DirectoryIterator');
    }
    private function node_is_spl_file_object(Class_Node $node): bool
    {
        $parent = $node->get_parent_class();
        return 'SplFileObject' === $parent || is_subclass_of($parent, 'SplFileObject');
    }
    private function node_is_symfony_spl_file_info(Class_Node $node): bool
    {
        $parent = $node->get_parent_class();
        return \Symfony\Component\Finder\Spl_File_Info::class === $parent;
    }
}