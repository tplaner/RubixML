<?php

namespace Rubix\ML\Tests\NeuralNet\CostFunctions;

use Rubix\Tensor\Matrix;
use Rubix\ML\NeuralNet\CostFunctions\Exponential;
use Rubix\ML\NeuralNet\CostFunctions\CostFunction;
use PHPUnit\Framework\TestCase;

class ExponentialTest extends TestCase
{
    protected $costFunction;

    protected $expected;

    protected $activation;

    protected $delta;

    public function setUp()
    {
        $this->expected = Matrix::quick([[36.], [22.], [18.], [41.5], [38.]]);

        $this->activation = Matrix::quick([[33.98], [20.], [4.6], [44.2], [38.5]]);

        $this->delta = Matrix::quick([
            [59.16913277009148],
            [54.59815003314423],
            [9.592176702893123E+77],
            [1465.5706972040061],
            [1.2840254166877414],
        ]);

        $this->costFunction = new Exponential(1.);
    }

    public function test_build_cost_function()
    {
        $this->assertInstanceOf(Exponential::class, $this->costFunction);
        $this->assertInstanceOf(CostFunction::class, $this->costFunction);
    }

    public function test_compute()
    {
        $cost = $this->costFunction
            ->compute($this->expected, $this->activation)
            ->asArray();

        $this->assertEquals($this->delta->asArray(), $cost);
    }

    public function test_differentiate()
    {
        $derivative = $this->costFunction
            ->differentiate($this->expected, $this->activation, $this->delta)
            ->asArray();

        $outcome = [
            [-239.04329639116995],
            [-218.39260013257692],
            [-2.570703356375357E+79],
            [7914.081764901642],
            [1.2840254166877414],
        ];

        $this->assertEquals($outcome, $derivative);
    }
}
