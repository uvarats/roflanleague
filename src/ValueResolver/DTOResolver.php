<?php

declare(strict_types=1);

namespace App\ValueResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Uvarats\Dto\Data;

class DTOResolver implements ValueResolverInterface
{

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if ($type === null || !is_subclass_of($type, Data::class)) {
            return [];
        }

        $argumentData = $request->toArray();

        if ($argumentData === []) {
            return [];
        }

        if (count($argumentData) === 1 && isset($argumentData['data'])) {
            $argumentData = $argumentData['data'];
        }

        return [$type::from($argumentData)];
    }
}