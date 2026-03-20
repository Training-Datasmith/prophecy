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
namespace Prophecy\Call;

use Exception;
use Prophecy\Argument\Arguments_Wildcard;
/**
 * Call object.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Call
{
    /**
     * @var string|null
     */
    private $file;
    private ?int $line = null;
    /**
     * @var \SplObjectStorage<ArgumentsWildcard, int|false>
     */
    private \Spl_Object_Storage $scores;
    /**
     * Initializes call.
     *
     * @param string      $methodName
     * @param array<mixed> $arguments
     * @param mixed       $returnValue
     * @param null|string $file
     * @param null|int    $line
     */
    public function __construct(private $method_name, private readonly array $arguments, private $return_value, private readonly ?Exception $exception, $file, $line)
    {
        $this->scores = new \Spl_Object_Storage();
        if ($file) {
            $this->file = $file;
            $this->line = intval($line);
        }
    }
    /**
     * Returns called method name.
     *
     * @return string
     */
    public function get_method_name()
    {
        return $this->method_name;
    }
    /**
     * Returns called method arguments.
     *
     * @return array<mixed>
     */
    public function get_arguments()
    {
        return $this->arguments;
    }
    /**
     * Returns called method return value.
     *
     * @return null|mixed
     */
    public function get_return_value()
    {
        return $this->return_value;
    }
    /**
     * Returns exception that call thrown.
     *
     * @return null|Exception
     */
    public function get_exception()
    {
        return $this->exception;
    }
    /**
     * Returns callee filename.
     *
     * @return string|null
     */
    public function get_file()
    {
        return $this->file;
    }
    /**
     * Returns callee line number.
     *
     * @return int|null
     */
    public function get_line()
    {
        return $this->line;
    }
    /**
     * Returns short notation for callee place.
     */
    public function get_call_place(): string
    {
        if (null === $this->file) {
            return 'unknown';
        }
        return sprintf('%s:%d', $this->file, $this->line);
    }
    /**
     * Adds the wildcard match score for the provided wildcard.
     *
     * @param false|int $score
     * @return $this
     */
    public function add_score(Arguments_Wildcard $wildcard, $score): static
    {
        $this->scores[$wildcard] = $score;
        return $this;
    }
    /**
     * Returns wildcard match score for the provided wildcard. The score is
     * calculated if not already done.
     *
     *
     * @return false|int False OR integer score (higher - better)
     */
    public function get_score(Arguments_Wildcard $wildcard)
    {
        return $this->scores[$wildcard] ?? $this->scores[$wildcard] = $wildcard->score_arguments($this->get_arguments());
    }
}