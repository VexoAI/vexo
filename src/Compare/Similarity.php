<?php

declare(strict_types=1);

namespace Vexo\Compare;

final class Similarity
{
    /**
     * Returns the average of cosine distances between vectors a and b.
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function cosine(array $a, array $b): float
    {
        $p = 0;
        $p2 = 0;
        $q2 = 0;
        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $p += $a[$i] * $b[$i];
            $p2 += $a[$i] * $a[$i];
            $q2 += $b[$i] * $b[$i];
        }
        return $p / (sqrt($p2) * sqrt($q2));
    }

    /**
     * Returns the Czekanowski similarity between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Czekanowski similarity
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function czekanowski(array $a, array $b): float
    {
        $up = 0;
        $down = 0;
        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $up += min($a[$i], $b[$i]);
            $down += $a[$i] + $b[$i];
        }
        return (2 * $up) / $down;
    }

    /**
     * Returns the Dice similarity between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Dice similarity algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function dice(array $a, array $b): float
    {
        return 1 - Distance::dice($a, $b);
    }

    /**
     * Returns the Intersection similarity distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Intersection similarity algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function intersection(array $a, array $b): float
    {
        return 1 - Distance::intersection($a, $b);
    }

    /**
     * Returns the Kulczynski similarity between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Kulczinski algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function kulczynski(array $a, array $b): float
    {
        return 1 / Distance::kulczynski($a, $b);
    }

    /**
     * Returns Kumar-Hassebrook similarity between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Kumar-Hassebrook Similarity algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function kumarHassebrook(array $a, array $b): float
    {
        $p = 0;
        $p2 = 0;
        $q2 = 0;
        $total = count($a);
        for ($i = 0; $i < $total; $i++) {
            $p += $a[$i] * $b[$i];
            $p2 += $a[$i] * $a[$i];
            $q2 += $b[$i] * $b[$i];
        }

        return $p / ($p2 + $q2 - $p);
    }

    /**
     * Returns the Motyka similarity between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Motyka algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function motyka(array $a, array $b): float
    {
        return 1 - Distance::motyka($a, $b);
    }

    /**
     * Returns the Pearson similarity between vectors a and b.
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function pearson(array $a, array $b): float
    {
        $total = count($a);

        $avgA = array_sum($a) / $total;
        $avgB = array_sum($b) / count($b);

        $newA = [];
        $newB = [];
        for ($i = 0; $i < $total; $i++) {
            $newA[$i] = $a[$i] - $avgA;
            $newB[$i] = $b[$i] - $avgB;
        }

        return self::cosine($newA, $newB);
    }

    /**
     * Returns the Squared-chord distance between vectors a and b.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Squared-chord algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     */
    public static function squaredChord(array $a, array $b): float
    {
        return 1 - Distance::squaredChord($a, $b);
    }

    /**
     * Returns the Tanimoto similarity between vectors p and q, and accepts the bitVector use.
     *
     * @link https://www.naun.org/main/NAUN/ijmmas/mmmas-49.pdf Tanimoto similarity algorithm
     *
     * @param float[] $a First vector
     * @param float[] $b Second vector
     * @param bool $bitvector BitVector
     */
    public static function tanimoto(array $a, array $b, bool $bitvector = false): float
    {
        $total = count($a);

        if ($bitvector) {
            $inter = 0;
            $union = 0;

            for ($i = 0; $i < $total; $i++) {
                $inter += $a[$i] && $b[$i];
                $union += $a[$i] || $b[$i];
            }

            if ($union === 0) {
                return 1;
            }

            return $inter / $union;
        }

        $p = 0;
        $q = 0;
        $m = 0;
        for ($i = 0; $i < $total; $i++) {
            $p += $a[$i];
            $q += $b[$i];
            $m += min($a[$i], $b[$i]);
        }

        return 1 - ($p + $q - 2 * $m) / ($p + $q - $m);
    }
}
