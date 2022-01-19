<?php

declare(strict_types = 1);

namespace App\Example;

class Circle implements MathShapeInterface, TwoDimensionsInterface
{
    private SampleInterface $neighborObject;

    /**
     * @param SampleInterface $neighborObject
     */
    public function __construct(SampleInterface $neighborObject)
    {
        $this->neighborObject = $neighborObject;
    }

    /**
     * @return SampleInterface
     */
    public function getNeighborObject(): SampleInterface
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
