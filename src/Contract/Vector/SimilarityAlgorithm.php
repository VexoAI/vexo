<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector;

enum SimilarityAlgorithm: string
{
    case COSINE = 'cosine';
    case CZEKANOWSKI = 'czekanowski';
    case DICE = 'dice';
    case INTERSECTION = 'intersection';
    case KULCZYNSKI = 'kulczynski';
    case KUMAR_HASSEBROOK = 'kumarHassebrook';
    case MOTYKA = 'motyka';
    case PEARSON = 'pearson';
    case SQUARED_CHORD = 'squaredChord';
    case TANIMOTO = 'tanimoto';
}
