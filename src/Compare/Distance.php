<?php

declare(strict_types=1);

namespace Vexo\Compare;

final class Distance
{
    /**
     * Returns the Additive Symmetric distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Additive Symmetric algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function additiveSymmetric(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += (($a[$i] - $b[$i]) * ($a[$i] - $b[$i]) * ($a[$i] + $b[$i])) / ($a[$i] * $b[$i]);
        }

        return $d;
    }

    /**
     * Returns the average of city block and Chebyshev distances between vectors a and b.
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function avg(array $a, array $b): float
    {
        $max = 0;
        $ans = 0;
        $aux = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $aux = abs($a[$i] - $b[$i]);
            $ans += $aux;
            if ($max < $aux) {
                $max = $aux;
            }
        }

        return ($max + $ans) / 2;
    }

    /**
     * Returns the Bhattacharyy distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Bhattacharyy algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function bhattacharyya(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += sqrt($a[$i] * $b[$i]);
        }

        return -log($ans);
    }

    /**
     * Returns the Canberra distance between vectors a and b.
     *
     * @link https://en.wikipedia.org/wiki/Canberra_distance Canberra algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function canberra(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += abs($a[$i] - $b[$i]) / ($a[$i] + $b[$i]);
        }

        return $ans;
    }

    /**
     * Returns the Chebyshev distance between vectors a and b.
     *
     * @link https://en.wikipedia.org/wiki/Chebyshev_distance Chebyshev algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function chebyshev(array $a, array $b): float
    {
        $max = 0;
        $aux = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $aux = abs($a[$i] - $b[$i]);
            if ($max < $aux) {
                $max = $aux;
            }
        }

        return $max;
    }

    /**
     * Returns the Clark distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Clark algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function clark(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += (abs($a[$i] - $b[$i]) / ($a[$i] + $b[$i])) ** 2;
        }

        return sqrt($d);
    }

    /**
     * Returns the Czekanowski distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Czekanowski algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function czekanowski(array $a, array $b): float
    {
        return 1 - Similarity::czekanowski($a, $b);
    }

    /**
     * Returns the Dice distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Dice algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function dice(array $a, array $b): float
    {
        $a2 = 0;
        $b2 = 0;
        $prod2 = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $a2 += $a[$i] * $a[$i];
            $b2 += $b[$i] * $b[$i];
            $prod2 += ($a[$i] - $b[$i]) * ($a[$i] - $b[$i]);
        }

        return $prod2 / ($a2 + $b2);
    }

    /**
     * Returns the Divergence distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Divergence algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function divergence(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += (($a[$i] - $b[$i]) * ($a[$i] - $b[$i])) / (($a[$i] + $b[$i]) * ($a[$i] + $b[$i]));
        }

        return 2 * $d;
    }

    /**
     * Returns the Euclidean distance between vectors a and b.
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function euclidean(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += ($a[$i] - $b[$i]) * ($a[$i] - $b[$i]);
        }

        return sqrt($d);
    }

    /**
     * Returns the Fidelity similarity between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Fidelity Similarity algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function fidelity(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += sqrt($a[$i] * $b[$i]);
        }

        return $ans;
    }

    /**
     * Returns the Gower distance between vectors a and b.
     *
     * @link https://stat.ethz.ch/education/semesters/ss2012/ams/slides/v4.2.pdf Gower algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function gower(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += abs($a[$i] - $b[$i]);
        }

        return $ans / $total;
    }

    /**
     * Returns the Harmonic mean similarity between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Harmonic Mean Similarity algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function harmonicMean(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += ($a[$i] * $b[$i]) / ($a[$i] + $b[$i]);
        }

        return 2 * $ans;
    }

    /**
     * Returns the Hellinger distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Hellinger algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function hellinger(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += sqrt($a[$i] * $b[$i]);
        }

        return 2 * sqrt(1 - $ans);
    }

    /**
     * Returns the Inner Product similarity between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Inner Product Similarity algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function innerProduct(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += $a[$i] * $b[$i];
        }

        return $ans;
    }

    /**
     * Returns the Intersection distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Intersection algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function intersection(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += min($a[$i], $b[$i]);
        }

        return 1 - $ans;
    }

    /**
     * Returns Jaccard distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Jaccard algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function jaccard(array $a, array $b): float
    {
        return 1 - Similarity::kumarHassebrook($a, $b);
    }

    /**
     * Returns the Jeffreys distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Jeffreys algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function jeffreys(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += ($a[$i] - $b[$i]) * log($a[$i] / $b[$i]);
        }

        return $ans;
    }

    /**
     * Returns the Jensen difference distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Jensen difference algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function jensenDifference(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans +=
                ($a[$i] * log($a[$i]) + $b[$i] * log($b[$i])) / 2 -
                (($a[$i] + $b[$i]) / 2) * log(($a[$i] + $b[$i]) / 2);
        }

        return $ans;
    }

    /**
     * Returns the Jensen-Shannon distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Jensen-Shannon algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function jensenShannon(array $a, array $b): float
    {
        $p = 0;
        $q = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $p += $a[$i] * log((2 * $a[$i]) / ($a[$i] + $b[$i]));
            $q += $b[$i] * log((2 * $b[$i]) / ($a[$i] + $b[$i]));
        }

        return ($p + $q) / 2;
    }

    /**
     * Returns the K divergence distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf K divergence algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function kdivergence(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += $a[$i] * log((2 * $a[$i]) / ($a[$i] + $b[$i]));
        }

        return $ans;
    }

    /**
     * Returns the Kulczynski distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Kulczynski algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function kulczynski(array $a, array $b): float
    {
        $up = 0;
        $down = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $up += abs($a[$i] - $b[$i]);
            $down += min($a[$i], $b[$i]);
        }

        return $up / $down;
    }

    /**
     * Returns the Kullback-Leibler distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Kullback-Leibler algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function kullbackLeibler(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += $a[$i] * log($a[$i] / $b[$i]);
        }

        return $ans;
    }

    /**
     * Returns the Kumar-Johnson distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Kumar-Johnson algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function kumarJohnson(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += pow(($a[$i] * $a[$i] - $b[$i] * $b[$i]), 2) / (2 * pow(($a[$i] * $b[$i]), 1.5));
        }

        return $ans;
    }

    /**
     * Returns the Lorentzian distance between vectors a and b.
     *
     * @link https://stat.ethz.ch/education/semesters/ss2012/ams/slides/v4.2.pdf Lorentzian algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function lorentzian(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += log(abs($a[$i] - $b[$i]) + 1);
        }

        return $ans;
    }

    /**
     * Returns the Manhattan distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Manhattan algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function manhattan(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += abs($a[$i] - $b[$i]);
        }

        return $d;
    }

    /**
     * Returns the Matusita distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Matusita algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function matusita(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += sqrt($a[$i] * $b[$i]);
        }

        return sqrt(2 - 2 * $ans);
    }

    /**
     * Returns the Minkowski distance between vectors a and b for order p.
     *
     * @link https://en.wikipedia.org/wiki/Minkowski_distance Minkowski algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     * @param float $p Number of order
     */
    public static function minkowski(array $a, array $b, float $p): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += pow(abs($a[$i] - $b[$i]), $p);
        }

        return pow($d, 1 / $p);
    }

    /**
     * Returns the Motyka distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Motyka algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function motyka(array $a, array $b): float
    {
        $up = 0;
        $down = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $up += min($a[$i], $b[$i]);
            $down += $a[$i] + $b[$i];
        }

        return 1 - $up / $down;
    }

    /**
     * Returns the Neyman distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Neyman algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function neyman(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += (($a[$i] - $b[$i]) * ($a[$i] - $b[$i])) / $a[$i];
        }

        return $d;
    }

    /**
     * Returns the Pearson distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Pearson algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function pearson(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += (($a[$i] - $b[$i]) * ($a[$i] - $b[$i])) / $b[$i];
        }

        return $d;
    }

    /**
     * Returns the Probabilistic Symmetric distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Probabilistic Symmetric algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function probabilisticSymmetric(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += (($a[$i] - $b[$i]) * ($a[$i] - $b[$i])) / ($a[$i] + $b[$i]);
        }

        return 2 * $d;
    }

    /**
     * Returns the Ruzicka distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Ruzicka algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function ruzicka(array $a, array $b): float
    {
        $up = 0;
        $down = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $up += min($a[$i], $b[$i]);
            $down += max($a[$i], $b[$i]);
        }

        return $up / $down;
    }

    /**
     * Returns the Soergel distance between vectors a and b.
     *
     * @link https://www.orgchm.bas.bg/ Soergel algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function soergel(array $a, array $b): float
    {
        $up = 0;
        $down = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $up += abs($a[$i] - $b[$i]);
            $down += max($a[$i], $b[$i]);
        }

        return $up / $down;
    }

    /**
     * Returns the Sorensen distance between vectors a and b.
     *
     * @link https://en.wikipedia.org/wiki/S%C3%B8rensen%E2%80%93Dice_coefficient Sorensen algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function sorensen(array $a, array $b): float
    {
        $up = 0;
        $down = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $up += abs($a[$i] - $b[$i]);
            $down += $a[$i] + $b[$i];
        }

        return $up / $down;
    }

    /**
     * Returns the squared distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Squared algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function squared(array $a, array $b): float
    {
        $d = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $d += (($a[$i] - $b[$i]) * ($a[$i] - $b[$i])) / ($a[$i] + $b[$i]);
        }

        return $d;
    }

    /**
     * Returns the Squared Chord distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Squared Chord algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function squaredChord(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += pow(sqrt($a[$i]) - sqrt($b[$i]), 2);
        }

        return $ans;
    }

    /**
     * Returns the Taneja distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Taneja algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function taneja(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans +=
                (($a[$i] + $b[$i]) / 2) *
                log(($a[$i] + $b[$i]) / (2 * sqrt($a[$i] * $b[$i])));
        }

        return $ans;
    }

    /**
     * Returns the Tanimoto distance between vectors a and b, and accepts the bitVector use.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Tanimoto algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     * @param bool $bitvector BitVector
     */
    public static function tanimoto(array $a, array $b, bool $bitvector = false): float
    {
        if ($bitvector) {
            return 1 - Similarity::tanimoto($a, $b, $bitvector);
        }

        $p = 0;
        $q = 0;
        $m = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $p += $a[$i];
            $q += $b[$i];
            $m += min($a[$i], $b[$i]);
        }

        return ($p + $q - 2 * $m) / ($p + $q - $m);
    }

    /**
     * Returns the Topsoe distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Topsoe algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function topsoe(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += $a[$i] * log((2 * $a[$i]) / ($a[$i] + $b[$i])) + $b[$i] * log((2 * $b[$i]) / ($a[$i] + $b[$i]));
        }

        return $ans;
    }

    /**
     * Returns the Wave Hedges distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Wave Hedges algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function waveHedges(array $a, array $b): float
    {
        $ans = 0;

        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $ans += 1 - min($a[$i], $b[$i]) / max($a[$i], $b[$i]);
        }

        return $ans;
    }
}
