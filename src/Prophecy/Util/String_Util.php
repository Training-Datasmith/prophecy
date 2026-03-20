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
namespace Prophecy\Util;

use Prophecy\Call\Call;
/**
 * String utility.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class String_Util
{
    /**
     * @param bool $verbose
     */
    public function __construct(private $verbose = true)
    {
    }
    /**
     * Stringifies any provided value.
     *
     * @param mixed   $value
     * @param boolean $exportObject
     *
     * @return string
     */
    public function stringify($value, $export_object = true)
    {
        if (\is_array($value)) {
            if (range(0, count($value) - 1) === array_keys($value)) {
                return '[' . implode(', ', array_map([$this, __FUNCTION__], $value)) . ']';
            }
            $stringify = [$this, __FUNCTION__];
            return '[' . implode(', ', array_map(fn($item, int|string $key) => (is_integer($key) ? $key : '"' . $key . '"') . ' => ' . call_user_func($stringify, $item), $value, array_keys($value))) . ']';
        }
        if (\is_resource($value)) {
            return get_resource_type($value) . ':' . $value;
        }
        if (\is_object($value)) {
            return $export_object ? Export_Util::export($value) : sprintf('%s#%s', $value::class, spl_object_id($value));
        }
        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (\is_string($value)) {
            $str = sprintf('"%s"', str_replace("\n", '\n', $value));
            if (!$this->verbose && 50 <= strlen($str)) {
                return substr($str, 0, 50) . '"...';
            }
            return $str;
        }
        if (null === $value) {
            return 'null';
        }
        \assert(\is_int($value) || \is_float($value));
        return (string) $value;
    }
    /**
     * Stringifies provided array of calls.
     *
     * @param Call[] $calls Array of Call instances
     */
    public function stringify_calls(array $calls): string
    {
        $self = $this;
        return implode(PHP_EOL, array_map(fn(Call $call) => sprintf('  - %s(%s) @ %s', $call->get_method_name(), implode(', ', array_map($self->stringify(...), $call->get_arguments())), str_replace(GETCWD() . DIRECTORY_SEPARATOR, '', $call->get_call_place())), $calls));
    }
}