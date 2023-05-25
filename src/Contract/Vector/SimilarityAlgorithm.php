<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector;

enum SimilarityAlgorithm: string
{
    case COSINE = 'Vexo\Compare\Similarity\cosine';
    case CZEKANOWSKI = 'Vexo\Compare\Similarity\czekanowski';
    case DICE = 'Vexo\Compare\Similarity\dice';
    case INTERSECTION = 'Vexo\Compare\Similarity\intersection';
    case KULCZYNSKI = 'Vexo\Compare\Similarity\kulczynski';
    case KUMAR_HASSEBROOK = 'Vexo\Compare\Similarity\kumarHassebrook';
    case MOTYKA = 'Vexo\Compare\Similarity\motyka';
    case PEARSON = 'Vexo\Compare\Similarity\pearson';
    case SQUARED_CHORD = 'Vexo\Compare\Similarity\squaredChord';
    case TANIMOTO = 'Vexo\Compare\Similarity\tanimoto';
}
