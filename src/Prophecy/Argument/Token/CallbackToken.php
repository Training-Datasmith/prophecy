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
 * Callback-verified token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Callback_Token implements Token_Interface
{
    private $callback;
    /**
     * Initializes token.
     *
     * @param callable $callback
     * @param string|null $customStringRepresentation Customize the __toString() representation of this token
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function __construct($callback, private readonly ?string $custom_string_representation = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf('Callable expected as an argument to CallbackToken, but got %s.', gettype($callback)));
        }
        $this->callback = $callback;
    }
    /**
     * Scores 7 if callback returns true, false otherwise.
     *
     * @param mixed $argument
     *
     * @return false|int
     */
    public function score_argument($argument): int|false
    {
        return call_user_func($this->callback, $argument) ? 7 : false;
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
        if ($this->custom_string_representation !== null) {
            return $this->custom_string_representation;
        }
        return 'callback()';
    }
}