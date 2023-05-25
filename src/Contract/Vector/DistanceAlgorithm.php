<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector;

enum DistanceAlgorithm: string
{
    case ADDITIVE_SYMMETRIC = 'Vexo\Compare\Distance\additiveSymmetric';
    case AVG = 'Vexo\Compare\Distance\avg';
    case BHATTACHARYYA = 'Vexo\Compare\Distance\bhattacharyya';
    case CANBERRA = 'Vexo\Compare\Distance\canberra';
    case CHEBYSHEV = 'Vexo\Compare\Distance\chebyshev';
    case CLARK = 'Vexo\Compare\Distance\clark';
    case CZEKANOWSKI = 'Vexo\Compare\Distance\czekanowski';
    case DICE = 'Vexo\Compare\Distance\dice';
    case DIVERGENCE = 'Vexo\Compare\Distance\divergence';
    case EUCLIDEAN = 'Vexo\Compare\Distance\euclidean';
    case FIDELITY = 'Vexo\Compare\Distance\fidelity';
    case GOWER = 'Vexo\Compare\Distance\gower';
    case HARMONIC_MEAN = 'Vexo\Compare\Distance\harmonicMean';
    case HELLINGER = 'Vexo\Compare\Distance\hellinger';
    case INNER_PRODUCT = 'Vexo\Compare\Distance\innerProduct';
    case INTERSECTION = 'Vexo\Compare\Distance\intersection';
    case JACCARD = 'Vexo\Compare\Distance\jaccard';
    case JEFFREYS = 'Vexo\Compare\Distance\jeffreys';
    case JENSEN_DIFFERENCE = 'Vexo\Compare\Distance\jensenDifference';
    case JENSEN_SHANNON = 'Vexo\Compare\Distance\jensenShannon';
    case K_DIVERGENCE = 'Vexo\Compare\Distance\kdivergence';
    case KULCZYNSKI = 'Vexo\Compare\Distance\kulczynski';
    case KULLBACK_LEIBLER = 'Vexo\Compare\Distance\kullbackLeibler';
    case KUMAR_JOHNSON = 'Vexo\Compare\Distance\kumarJohnson';
    case LORENTZIAN = 'Vexo\Compare\Distance\lorentzian';
    case MANHATTAN = 'Vexo\Compare\Distance\manhattan';
    case MATUSITA = 'Vexo\Compare\Distance\matusita';
    case MOTYKA = 'Vexo\Compare\Distance\motyka';
    case NEYMAN = 'Vexo\Compare\Distance\neyman';
    case PEARSON = 'Vexo\Compare\Distance\pearson';
    case PROBABILISTIC_SYMMETRIC = 'Vexo\Compare\Distance\probabilisticSymmetric';
    case RUZICKA = 'Vexo\Compare\Distance\ruzicka';
    case SOERGEL = 'Vexo\Compare\Distance\soergel';
    case SORENSEN = 'Vexo\Compare\Distance\sorensen';
    case SQUARED = 'Vexo\Compare\Distance\squared';
    case SQUARED_CHORD = 'Vexo\Compare\Distance\squaredChord';
    case TANEJA = 'Vexo\Compare\Distance\taneja';
    case TANIMOTO = 'Vexo\Compare\Distance\tanimoto';
    case TOPSOE = 'Vexo\Compare\Distance\topsoe';
    case WAVE_HEDGES = 'Vexo\Compare\Distance\waveHedges';
}
