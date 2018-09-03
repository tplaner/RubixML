<?php

namespace Rubix\ML\Other\Structures;

use InvalidArgumentException;
use IteratorAggregate;
use RuntimeException;
use ArrayIterator;
use ArrayAccess;
use Countable;

/**
 * Vector
 *
 * One dimensional tensor with integer and/or floating point elements.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class Vector implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * The 1-d array that holds the values of the vector.
     *
     * @var array
     */
    protected $a = [
        //
    ];

    /**
     * The elements in the vector.
     *
     * @var int
     */
    protected $n;

    /**
     * Build a vector of zeros with n elements.
     *
     * @param  int  $n
     * @return self
     */
    public static function zeros(int $n) : self
    {
        return new self(array_fill(0, $n, 0), false);
    }

    /**
     * Build a vector of ones with n elements.
     *
     * @param  int  $n
     * @return self
     */
    public static function ones(int $n) : self
    {
        return new self(array_fill(0, $n, 1), false);
    }

    /**
     * @param  (int|float)[]  $a
     * @param  bool  $validate
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(array $a, bool $validate = true)
    {
        if ($validate === true) {
            $a = array_values($a);

            foreach ($a as $value) {
                if (!is_int($value) and !is_float($value)) {
                    throw new InvalidArgumentException('Vector element must'
                        . ' be an integer or float, '
                        . gettype($value) . ' found.');
                }
            }
        }

        $this->a = $a;
        $this->n = count($a);
    }

    /**
     * Return the number of elements in the vector i.e the dimensionality.
     *
     * @return int
     */
    public function n() : int
    {
        return $this->n;
    }

    /**
     * Return the vector as an array.
     *
     * @return array
     */
    public function asArray() : array
    {
        return $this->a;
    }

    /**
     * The sum of the vector.
     *
     * @return float
     */
    public function sum() : float
    {
        return (float) array_sum($this->a);
    }

    /**
     * Compute the dot product of this vector and another vector.
     *
     * @param  \Rubix\ML\Other\Structures\Vector  $b
     * @throws \InvalidArgumentException
     * @return float
     */
    public function dot(Vector $b) : float
    {
        if ($this->n !== $b->n()) {
            throw new InvalidArgumentException('Vectors do not have the same'
                . ' dimensionality.');
        }

        $product = 0.;

        foreach ($this->a as $i => $value) {
            $product += $value * $b[$i];
        }

        return $product;
    }

    /**
     * Calculate the outer product of this and another vector. Return as Matrix.
     *
     * @param  \Rubix\ML\Other\Structures\Vector  $b
     * @return \Rubix\ML\Other\Structures\Matrix
     */
    public function outer(Vector $b) : Matrix
    {
        $n = $b->n();

        $c = [[]];

        foreach ($this->a as $i => $value) {
            for ($j = 0; $j < $n; $j++) {
                $c[$i][$j] = $value * $b[$j];
            }
        }

        return new Matrix($c);
    }

    /**
     * Multiply this vector with another vector.
     *
     * @param  \Rubix\ML\Other\Structures\Vector  $b
     * @throws \InvalidArgumentException
     * @return self
     */
    public function multiply(Vector $b) : self
    {
        if ($this->n !== $b->n()) {
            throw new InvalidArgumentException('Vectors do not have the same'
                . ' dimensionality.');
        }

        $c = [];

        foreach ($this->a as $i => $value) {
            $c[$i] = $value * $b[$i];
        }

        return new self($c, false);
    }

    /**
     * Divide this vector by another vector.
     *
     * @param  \Rubix\ML\Other\Structures\Vector  $b
     * @throws \InvalidArgumentException
     * @return self
     */
    public function divide(Vector $b) : self
    {
        if ($this->n !== $b->n()) {
            throw new InvalidArgumentException('Vectors do not have the same'
                . ' dimensionality.');
        }

        $c = [];

        foreach ($this->a as $i => $value) {
            $c[$i] = $value / $b[$i];
        }

        return new self($c, false);
    }

    /**
     * Add this vector to another vector.
     *
     * @param  \Rubix\ML\Other\Structures\Vector  $b
     * @throws \InvalidArgumentException
     * @return self
     */
    public function add(Vector $b) : self
    {
        if ($this->n !== $b->n()) {
            throw new InvalidArgumentException('Vectors do not have the same'
                . ' dimensionality.');
        }

        $c = [];

        foreach ($this->a as $i => $value) {
            $c[$i] = $value + $b[$i];
        }

        return new self($c, false);
    }

    /**
     * Subtract this vector from another vector.
     *
     * @param  \Rubix\ML\Other\Structures\Vector  $b
     * @throws \InvalidArgumentException
     * @return self
     */
    public function subtract(Vector $b) : self
    {
        if ($this->n !== $b->n()) {
            throw new InvalidArgumentException('Vectors do not have the same'
                . ' dimensionality.');
        }

        $c = [];

        foreach ($this->a as $i => $value) {
            $c[$i] = $value - $b[$i];
        }

        return new self($c, false);
    }

    /**
     * Multiply this matrix by a scalar.
     *
     * @param  mixed  $scalar
     * @throws \InvalidArgumentException
     * @return self
     */
    public function scalarMultiply($scalar) : self
    {
        if (!is_int($scalar) and !is_float($scalar)) {
            throw new InvalidArgumentException('Scalar must be an integer or'
                . ' float ' . gettype($scalar) . ' found.');
        }

        $b = [];

        foreach ($this->a as $i => $value) {
            $b[$i] = $value * $scalar;
        }

        return new self($b, false);
    }

    /**
     * Divide this matrix by a scalar.
     *
     * @param  mixed  $scalar
     * @throws \InvalidArgumentException
     * @return self
     */
    public function scalarDivide($scalar) : self
    {
        if (!is_int($scalar) and !is_float($scalar)) {
            throw new InvalidArgumentException('Scalar must be an integer or'
                . ' float ' . gettype($scalar) . ' found.');
        }

        $b = [];

        foreach ($this->a as $i => $value) {
            $b[$i] = $value / $scalar;
        }

        return new self($b, false);
    }

    /**
     * Add this matrix by a scalar.
     *
     * @param  mixed  $scalar
     * @throws \InvalidArgumentException
     * @return self
     */
    public function scalarAdd($scalar) : self
    {
        if (!is_int($scalar) and !is_float($scalar)) {
            throw new InvalidArgumentException('Factor must be an integer or'
                . ' float ' . gettype($scalar) . ' found.');
        }

        $b = [];

        foreach ($this->a as $i => $value) {
            $b[$i] = $value + $scalar;
        }

        return new self($b, false);
    }

    /**
     * Subtract this matrix by a scalar.
     *
     * @param  mixed  $scalar
     * @throws \InvalidArgumentException
     * @return self
     */
    public function scalarSubtract($scalar) : self
    {
        if (!is_int($scalar) and !is_float($scalar)) {
            throw new InvalidArgumentException('Scalar must be an integer or'
                . ' float ' . gettype($scalar) . ' found.');
        }

        $b = [];

        foreach ($this->a as $i => $value) {
            $b[$i] = $value - $scalar;
        }

        return new self($b, false);
    }

    /**
     * Exponentiate each element in the vector.
     *
     * @return self
     */
    public function exp() : self
    {
        $b = [];

        foreach ($this->a as $value) {
            $b[] = M_E ** $value;
        }

        return new self($b, false);
    }

    /**
     * Calculate the L1 or Manhattan norm of the vector.
     *
     * @return float
     */
    public function l1Norm() : float
    {
        $norm = 0.;

        foreach ($this->a as $value) {
            $norm += abs($value);
        }

        return $norm;
    }

    /**
     * Calculate the L2 or Euclidean norm of the vector.
     *
     * @return float
     */
    public function l2Norm() : float
    {
        $norm = 0.;

        foreach ($this->a as $value) {
            $norm += $value ** 2;
        }

        return $norm ** 0.5;
    }

    /**
     * @return int
     */
    public function count() : int
    {
        return $this->n;
    }

    /**
     * @param  mixed  $index
     * @param  array  $values
     * @throws \RuntimeException
     * @return void
     */
    public function offsetSet($index, $values) : void
    {
        throw new RuntimeException('Vector cannot be mutated directly.');
    }

    /**
     * Does a given column exist in the matrix.
     *
     * @param  mixed  $index
     * @return bool
     */
    public function offsetExists($index) : bool
    {
        return isset($this->a[$index]);
    }

    /**
     * @param  mixed  $index
     * @throws \RuntimeException
     * @return void
     */
    public function offsetUnset($index) : void
    {
        throw new RuntimeException('Vector cannot be mutated directly.');
    }

    /**
     * Return a row from the matrix at the given index.
     *
     * @param  mixed  $index
     * @throws \InvalidArgumentException
     * @return int|float
     */
    public function offsetGet($index)
    {
        if (!isset($this->a[$index])) {
            throw new InvalidArgumentException('Element not found at index'
                . (string) $index . '.');
        }

        return $this->a[$index];
    }

    /**
     * Get an iterator for the rows in the matrix.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->a);
    }
}
