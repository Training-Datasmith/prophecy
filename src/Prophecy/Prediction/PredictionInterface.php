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
namespace Prophecy\Prediction;

use Prophecy\Call\Call;
use Prophecy\Exception\Prediction\Prediction_Exception;
use Prophecy\Prophecy\Method_Prophecy;
use Prophecy\Prophecy\Object_Prophecy;
/**
 * Prediction interface.
 * Predictions are logical test blocks, tied to `should...` keyword.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface Prediction_Interface
{
    /**
     * Tests that double fulfilled prediction.
     *
     * @param Call[]        $calls
     * @param ObjectProphecy<object> $object
     *
     * @throws PredictionException
     * @return void
     */
    public function check(array $calls, Object_Prophecy $object, Method_Prophecy $method);
}