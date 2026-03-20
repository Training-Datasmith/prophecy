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

use Prophecy\Call\Call;
use Prophecy\Exception\Prophecy\Method_Prophecy_Exception;
use Prophecy\Prophecy\Method_Prophecy;
class Unexpected_Calls_Exception extends Method_Prophecy_Exception implements Prediction_Exception
{
    /** @var list<Call> */
    private readonly array $calls;
    /**
     * @param string         $message
     * @param array<Call>     $calls
     */
    public function __construct($message, Method_Prophecy $method_prophecy, array $calls)
    {
        parent::__construct($message, $method_prophecy);
        $this->calls = array_values($calls);
    }
    /**
     * @return list<Call>
     */
    public function get_calls()
    {
        return $this->calls;
    }
}