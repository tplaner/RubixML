<?php

namespace Rubix\ML\NeuralNet\Layers;

use Rubix\Tensor\Matrix;
use Rubix\ML\NeuralNet\Optimizers\Optimizer;
use InvalidArgumentException;
use RuntimeException;
use Closure;

/**
 * Dropout
 *
 * Dropout layers temporarily disable neurons during each training pass. Dropout
 * is a regularization technique for reducing overfitting in neural networks
 * by preventing complex co-adaptations on training data. It is a very efficient
 * way of performing model averaging with neural networks.
 *
 * References:
 * [1] N. Srivastava et al. (2014). Dropout: A Simple Way to Prevent Neural
 * Networks from Overfitting.
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class Dropout implements Hidden
{
    /**
     * The ratio of neurons that are dropped during each training pass.
     *
     * @var float
     */
    protected $ratio;

    /**
     * The scaling coefficient.
     *
     * @var float
     */
    protected $scale;

    /**
     * The width of the layer.
     *
     * @var int|null
     */
    protected $width;

    /**
     * The memoized dropout mask.
     *
     * @var \Rubix\Tensor\Matrix|null
     */
    protected $mask;

    /**
     * @param float $ratio
     * @throws \InvalidArgumentException
     */
    public function __construct(float $ratio = 0.5)
    {
        if ($ratio <= 0. or $ratio >= 1.) {
            throw new InvalidArgumentException('Dropout ratio must be between'
                . " 0 and 1, $ratio given.");
        }

        $this->ratio = $ratio;
        $this->scale = 1. / (1. - $ratio);
    }

    /**
     * Return the width of the layer.
     *
     * @return int|null
     */
    public function width() : ?int
    {
        return $this->width;
    }

    /**
     * Initialize the layer with the fan in from the previous layer and return
     * the fan out for this layer.
     *
     * @param int $fanIn
     * @return int
     */
    public function initialize(int $fanIn) : int
    {
        $fanOut = $fanIn;

        $this->width = $fanOut;

        return $fanOut;
    }

    /**
     * Compute a forward pass through the layer.
     *
     * @param \Rubix\Tensor\Matrix $input
     * @return \Rubix\Tensor\Matrix
     */
    public function forward(Matrix $input) : Matrix
    {
        $this->mask = Matrix::rand(...$input->shape())
            ->greater($this->ratio);

        return $this->mask->multiply($input);
    }

    /**
     * Compute an inferential pass through the layer.
     *
     * @param \Rubix\Tensor\Matrix $input
     * @return \Rubix\Tensor\Matrix
     */
    public function infer(Matrix $input) : Matrix
    {
        return $input;
    }

    /**
     * Calculate the gradients of the layer and update the parameters.
     *
     * @param Closure $prevGradient
     * @param \Rubix\ML\NeuralNet\Optimizers\Optimizer $optimizer
     * @throws \RuntimeException
     * @return Closure
     */
    public function back(Closure $prevGradient, Optimizer $optimizer) : Closure
    {
        if (!$this->mask) {
            throw new RuntimeException('Must perform forward pass before'
                . ' backpropagating.');
        }

        $mask = $this->mask;

        unset($this->mask);

        return function () use ($prevGradient, $mask) {
            return $prevGradient()->multiply($mask);
        };
    }
}
