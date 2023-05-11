# Vexo Compare

> ⚠️ this is a sub-split, for pull requests and issues, visit: https://github.com/VexoAI/vexo

A collection of functions to compare vectors. Initially ported from [mljs/distance](https://github.com/mljs/distance).

## Installation

```bash
composer require vexo/compare
```

## Usage

```php
<?php

declare(strict_types=1);

use Vexo\Compare\Distance;
use Vexo\Compare\Similarity;

$one = [0.21, -0.32, 0.01];
$two = [0.42, -0.11, -0.02];

// Get the euclidean distance between vector one and two
$distance = Distance::euclidean($one, $two);

// Get the average of cosine distances between vector one and two
$similarity = Similarity::cosine($one, $two);

```
