<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector;

enum DistanceAlgorithm: string
{
    case ADDITIVE_SYMMETRIC = 'additiveSymmetric';
    case AVG = 'avg';
    case BHATTACHARYYA = 'bhattacharyya';
    case CANBERRA = 'canberra';
    case CHEBYSHEV = 'chebyshev';
    case CLARK = 'clark';
    case CZEKANOWSKI = 'czekanowski';
    case DICE = 'dice';
    case DIVERGENCE = 'divergence';
    case EUCLIDEAN = 'euclidean';
    case FIDELITY = 'fidelity';
    case GOWER = 'gower';
    case HARMONIC_MEAN = 'harmonicMean';
    case HELLINGER = 'hellinger';
    case INNER_PRODUCT = 'innerProduct';
    case INTERSECTION = 'intersection';
    case JACCARD = 'jaccard';
    case JEFFREYS = 'jeffreys';
    case JENSEN_DIFFERENCE = 'jensenDifference';
    case JENSEN_SHANNON = 'jensenShannon';
    case K_DIVERGENCE = 'kdivergence';
    case KULCZYNSKI = 'kulczynski';
    case KULLBACK_LEIBLER = 'kullbackLeibler';
    case KUMAR_JOHNSON = 'kumarJohnson';
    case LORENTZIAN = 'lorentzian';
    case MANHATTAN = 'manhattan';
    case MATUSITA = 'matusita';
    case MOTYKA = 'motyka';
    case NEYMAN = 'neyman';
    case PEARSON = 'pearson';
    case PROBABILISTIC_SYMMETRIC = 'probabilisticSymmetric';
    case RUZICKA = 'ruzicka';
    case SOERGEL = 'soergel';
    case SORENSEN = 'sorensen';
    case SQUARED = 'squared';
    case SQUARED_CHORD = 'squaredChord';
    case TANEJA = 'taneja';
    case TANIMOTO = 'tanimoto';
    case TOPSOE = 'topsoe';
    case WAVE_HEDGES = 'waveHedges';
}
