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
namespace Prophecy\Php_Documentor;

use Php_Documentor\Reflection\Doc_Block\Tags\Method;
use Php_Documentor\Reflection\Doc_Block_Factory;
use Php_Documentor\Reflection\Types\Context_Factory;
/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @internal
 */
final class Class_Tag_Retriever implements Method_Tag_Retriever_Interface
{
    private $doc_block_factory;
    private $context_factory;
    public function __construct()
    {
        $this->doc_block_factory = Doc_Block_Factory::create_instance();
        $this->context_factory = new Context_Factory();
    }
    public function get_tag_list(\ReflectionClass $reflection_class): array
    {
        try {
            $phpdoc = $this->doc_block_factory->create($reflection_class, $this->context_factory->create_from_reflector($reflection_class));
            $methods = [];
            foreach ($phpdoc->get_tags_by_name('method') as $tag) {
                if ($tag instanceof Method) {
                    $methods[] = $tag;
                }
            }
            return $methods;
        } catch (\InvalidArgumentException) {
            return [];
        }
    }
}