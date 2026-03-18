<?php

namespace Prophecy\Exception\Doubler;

class MethodNotExtendableException extends DoubleException
{
    /**
     * @param string $message
     * @param string $className
     * @param string $methodName
     */
    public function __construct($message, private $className, private $methodName)
    {
        parent::__construct($message);
    }


    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

}
