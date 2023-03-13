<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Dto\Base\Data;
use App\Dto\Base\MapperFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class DTOResolver implements ValueResolverInterface
{

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if ($type === null || !is_subclass_of($type, Data::class)) {
            return [];
        }

        $argumentData = $request->request->get($argument->getName());

        if ($argumentData === null) {
            $argumentData = $request->getContent();
        }

        if (!is_string($argumentData)) {
            return [];
        }

        $factory = new MapperFactory();
        $mapper = $factory->getMapper();

        return [$mapper->map($argumentData, $type)];
    }
}