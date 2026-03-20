<?php

declare (strict_types=1);
namespace Prophecy\Exception\Doubler;

class Method_Not_Extendable_Exception extends Double_Exception
{
    /**
     * @param string $message
     * @param string $className
     * @param string $methodName
     */
    public function __construct($message, private $class_name, private $method_name)
    {
        parent::__construct($message);
    }
    /**
     * @return string
     */
    public function get_method_name()
    {
        return $this->method_name;
    }
    /**
     * @return string
     */
    public function get_class_name()
    {
        return $this->class_name;
    }
}