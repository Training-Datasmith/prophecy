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
namespace Prophecy\Exception\Prediction;

use Prophecy\Prophecy\Object_Prophecy;
class Aggregate_Exception extends \RuntimeException implements Prediction_Exception
{
    /**
     * @var list<PredictionException>
     */
    private array $exceptions = [];
    /**
     * @var ObjectProphecy<object>|null
     */
    private ?\Prophecy\Prophecy\Object_Prophecy $object_prophecy = null;
    public function append(Prediction_Exception $exception): void
    {
        $message = $exception->get_message();
        $message = strtr($message, ["\n" => "\n  "]) . "\n";
        $message = empty($this->exceptions) ? $message : "\n" . $message;
        $this->message = rtrim($this->message . $message);
        $this->exceptions[] = $exception;
    }
    /**
     * @return list<PredictionException>
     */
    public function get_exceptions()
    {
        return $this->exceptions;
    }
    /**
     * @param ObjectProphecy<object> $objectProphecy
     */
    public function set_object_prophecy(Object_Prophecy $object_prophecy): void
    {
        $this->object_prophecy = $object_prophecy;
    }
    /**
     * @return ObjectProphecy<object>|null
     */
    public function get_object_prophecy()
    {
        return $this->object_prophecy;
    }
}