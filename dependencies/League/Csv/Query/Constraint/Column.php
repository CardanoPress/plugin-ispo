<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CardanoPress\ISPO\Dependencies\League\Csv\Query\Constraint;

use ArrayIterator;
use CallbackFilterIterator;
use Closure;
use Iterator;
use IteratorIterator;
use CardanoPress\ISPO\Dependencies\League\Csv\Query;
use ReflectionException;
use Traversable;

/**
 * Enable filtering a record based on the value of a one of its cell.
 *
 * When used with PHP's array_filter with the ARRAY_FILTER_USE_BOTH flag
 * the record offset WILL NOT BE taken into account
 */
final class Column implements Query\Predicate
{
    /**
     * @throws Query\QueryException
     */
    private function __construct(
        public readonly string|int $column,
        public readonly Comparison|Closure $operator,
        public readonly mixed $value,
    ) {
        if (!$this->operator instanceof Closure) {
            $this->operator->accept($this->value);
        }
    }

    /**
     * @throws Query\QueryException
     */
    public static function filterOn(
        string|int $column,
        Comparison|Closure|string $operator,
        mixed $value = null,
    ): self {
        if ($operator instanceof Closure) {
            return new self($column, $operator, null);
        }

        return new self(
            $column,
            is_string($operator) ? Comparison::fromOperator($operator) : $operator,
            $value
        );
    }

    /**
     * @throws ReflectionException
     * @throws Query\QueryException
     */
    public function __invoke(mixed $value, int|string $key): bool
    {
        $subject = Query\Row::from($value)->value($this->column);
        if ($this->operator instanceof Closure) {
            return ($this->operator)($subject);
        }

        return $this->operator->compare($subject, $this->value);
    }

    public function filter(iterable $value): Iterator
    {
        return new CallbackFilterIterator(match (true) {
            $value instanceof Iterator => $value,
            $value instanceof Traversable => new IteratorIterator($value),
            default => new ArrayIterator($value),
        }, $this);
    }
}
