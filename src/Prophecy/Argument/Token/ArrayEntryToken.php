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
namespace Prophecy\Argument\Token;

use Prophecy\Exception\InvalidArgumentException;
/**
 * Array entry token.
 *
 * @author Boris Mikhaylov <kaguxmail@gmail.com>
 */
class Array_Entry_Token implements Token_Interface
{
    /** @var TokenInterface */
    private $key;
    /** @var TokenInterface */
    private $value;
    /**
     * @param mixed $key   exact value or token
     * @param mixed $value exact value or token
     */
    public function __construct($key, $value)
    {
        $this->key = $this->wrap_into_exact_value_token($key);
        $this->value = $this->wrap_into_exact_value_token($value);
    }
    /**
     * Scores half of combined scores from key and value tokens for same entry. Capped at 8.
     * If argument implements \ArrayAccess without \Traversable, then key token is restricted to ExactValueToken.
     *
     * @param mixed $argument
     *
     * @throws InvalidArgumentException
     * @return false|int
     */
    public function score_argument($argument): false|int
    {
        if ($argument instanceof \Traversable) {
            $argument = iterator_to_array($argument);
        }
        if ($argument instanceof \ArrayAccess) {
            $argument = $this->convert_array_access_to_entry($argument);
        }
        if (!is_array($argument) || empty($argument)) {
            return false;
        }
        $key_scores = array_map($this->key->score_argument(...), array_keys($argument));
        $value_scores = array_map($this->value->score_argument(...), $argument);
        $score_entry = static fn($value, $key) => $value && $key ? (int) min(8, ($key + $value) / 2) : false;
        return max(array_map($score_entry, $value_scores, $key_scores));
    }
    /**
     * Returns false.
     */
    public function is_last(): bool
    {
        return false;
    }
    /**
     * Returns string representation for token.
     */
    public function __toString(): string
    {
        return sprintf('[..., %s => %s, ...]', $this->key, $this->value);
    }
    /**
     * Returns key
     *
     * @return TokenInterface
     */
    public function get_key()
    {
        return $this->key;
    }
    /**
     * Returns value
     *
     * @return TokenInterface
     */
    public function get_value()
    {
        return $this->value;
    }
    /**
     * Wraps non token $value into ExactValueToken
     *
     * @param mixed $value
     */
    private function wrap_into_exact_value_token($value): \Prophecy\Argument\Token\Token_Interface
    {
        return $value instanceof Token_Interface ? $value : new Exact_Value_Token($value);
    }
    /**
     * Converts instance of \ArrayAccess to key => value array entry
     *
     * @param \ArrayAccess<array-key, mixed> $object
     *
     * @return array<mixed>
     * @throws InvalidArgumentException
     */
    private function convert_array_access_to_entry(\ArrayAccess $object): array
    {
        if (!$this->key instanceof Exact_Value_Token) {
            throw new InvalidArgumentException(sprintf('You can only use exact value tokens to match key of ArrayAccess object' . PHP_EOL . 'But you used `%s`.', $this->key));
        }
        $key = $this->key->get_value();
        if (!\is_int($key) && !\is_string($key)) {
            throw new InvalidArgumentException(sprintf('You can only use integer or string keys to match key of ArrayAccess object' . PHP_EOL . 'But you used `%s`.', $this->key));
        }
        return $object->offsetExists($key) ? [$key => $object[$key]] : [];
    }
}