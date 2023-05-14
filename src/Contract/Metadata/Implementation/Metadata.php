<?php

declare(strict_types=1);

namespace Vexo\Contract\Metadata\Implementation;

use Ramsey\Collection\Map\AbstractMap;
use Vexo\Contract\Metadata\Metadata as MetadataContract;

/**
 * @extends AbstractMap<string, mixed>
 */
final class Metadata extends AbstractMap implements MetadataContract
{
}
