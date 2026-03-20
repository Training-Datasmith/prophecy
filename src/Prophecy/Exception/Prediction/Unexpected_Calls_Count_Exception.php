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
use Prophecy\Prophecy\Method_Prophecy;
class Unexpected_Calls_Count_Exception extends Unexpected_Calls_Exception
{
    private readonly int $expected_count;
    /**
     * @param string         $message
     * @param int            $count
     * @param array<Call>     $calls
     */
    public function __construct($message, Method_Prophecy $method_prophecy, $count, array $calls)
    {
        parent::__construct($message, $method_prophecy, $calls);
        $this->expected_count = intval($count);
    }
    /**
     * @return int
     */
    public function get_expected_count()
    {
        return $this->expected_count;
    }
}