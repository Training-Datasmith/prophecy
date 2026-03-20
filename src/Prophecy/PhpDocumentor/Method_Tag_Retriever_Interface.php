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
/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 *
 * @internal
 */
interface Method_Tag_Retriever_Interface
{
    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return list<Method>
     */
    public function get_tag_list(\ReflectionClass $reflection_class);
}