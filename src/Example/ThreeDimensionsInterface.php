<?php

declare(strict_types = 1);

namespace App\Example;

interface ThreeDimensionsInterface extends MathShapeInterface
{
    public function doSomethingWithThreeDimensions(): void;
}
