<?php

declare(strict_types = 1);

namespace App\Example;

class Circle implements MathShapeInterface, TwoDimensionsInterface
{
    private MathShapeInterface $neighborObject;

    /**
     * @param MathShapeInterface $neighborObject
     */
    public function __construct(MathShapeInterface $neighborObject)
    {
        $this->neighborObject = $neighborObject;
    }

    /**
     * @return MathShapeInterface
     */
    public function getNeighborObject(): MathShapeInterface
    {
        return $this->neighborObject;
    }

    public function doSomethingWithMathShape(): void
    {
        // TODO: Implement doSomethingWithMathShape() method.
    }

    public function doSomethingWithTwoDimensionalObject(): void
    {
        // TODO: Implement doSomethingWithTwoDimensionalObject() method.
    }
}
