+++
title = "Benchmarking listing files in a local directory in PHP"
+++

Yesterday, I ran the benchmark no one was waiting for: all the different ways to list files in a local directory in PHP.

The methods I benchmarked were `scandir`, `readdir`, `glob` and `DirectoryIterator`.

Unsurprisingly, the runtime grows linearly with the number of directory entries.

A lot of time is spent on sorting, `scandir("", SCANDIR_SORT_NONE)` and `glob("*", GLOB_NOSORT")` are both at least 10-30% faster than their sorted versions (which is the default).

`scandir("", SCANDIR_SORT_NONE)` is consistently the fastest option, with `readdir` coming in second.

Interestingly, `DirectoryIterator` is the slowest option for directories with less than 2000 files, but then its relative performance increases until it is about as fast as `readdir` and `scandir` for directories with 10000 files.

You can view the raw results or the benchmarking code I used below. Times are in `ms` and taken as the average across 100 iterations.

![Benchmarking PHP directory listing](/2025/php-directory-listing-benchmark.svg)

### Benchmark results

| files | DirectoryIterator | readdir | scandir | scandir unsorted | glob  | glob unsorted |
| ----- | -------- | ------- | ------- | ---------------- | ----- | ------------- |
| 0     | 0.008    | 0.006   | 0.006   | 0.006            | 0.006 | 0.005         |
| 10    | 0.013    | 0.009   | 0.009   | 0.008            | 0.010 | 0.009         |
| 20    | 0.018    | 0.012   | 0.012   | 0.011            | 0.014 | 0.013         |
| 30    | 0.025    | 0.016   | 0.016   | 0.015            | 0.019 | 0.018         |
| 40    | 0.031    | 0.019   | 0.020   | 0.018            | 0.024 | 0.022         |
| 50    | 0.037    | 0.023   | 0.025   | 0.021            | 0.028 | 0.026         |
| 60    | 0.043    | 0.026   | 0.029   | 0.024            | 0.034 | 0.030         |
| 70    | 0.050    | 0.029   | 0.033   | 0.027            | 0.039 | 0.034         |
| 80    | 0.058    | 0.034   | 0.038   | 0.032            | 0.045 | 0.040         |
| 90    | 0.065    | 0.037   | 0.042   | 0.035            | 0.050 | 0.044         |
| 100   | 0.071    | 0.041   | 0.046   | 0.039            | 0.056 | 0.049         |
| 200   | 0.138    | 0.078   | 0.093   | 0.073            | 0.113 | 0.094         |
| 300   | 0.204    | 0.114   | 0.138   | 0.108            | 0.168 | 0.138         |
| 400   | 0.274    | 0.151   | 0.186   | 0.143            | 0.223 | 0.183         |
| 500   | 0.338    | 0.189   | 0.232   | 0.178            | 0.280 | 0.227         |
| 600   | 0.405    | 0.226   | 0.280   | 0.214            | 0.336 | 0.271         |
| 700   | 0.476    | 0.263   | 0.331   | 0.250            | 0.395 | 0.316         |
| 800   | 0.544    | 0.300   | 0.379   | 0.286            | 0.453 | 0.361         |
| 900   | 0.615    | 0.339   | 0.428   | 0.322            | 0.513 | 0.411         |
| 1000  | 0.683    | 0.376   | 0.477   | 0.357            | 0.568 | 0.449         |
| 2000  | 1.130    | 0.780   | 0.994   | 0.731            | 1.173 | 0.911         |
| 3000  | 1.464    | 1.175   | 1.519   | 1.099            | 1.781 | 1.364         |
| 4000  | 1.811    | 1.565   | 2.036   | 1.468            | 2.395 | 1.827         |
| 5000  | 2.191    | 1.948   | 2.558   | 1.818            | 2.996 | 2.250         |
| 6000  | 2.544    | 2.348   | 3.106   | 2.207            | 3.648 | 2.738         |
| 7000  | 2.942    | 2.754   | 3.659   | 2.654            | 4.405 | 3.153         |
| 8000  | 3.302    | 3.106   | 4.234   | 2.943            | 5.000 | 3.791         |
| 9000  | 3.633    | 3.622   | 4.866   | 3.319            | 5.973 | 4.191         |
| 10000 | 3.994    | 3.872   | 5.248   | 3.612            | 6.277 | 4.472         |

### Benchmark code

```php
<?php

namespace D;

class DirectoryReader
{
    public function __construct(
        protected string $directory,
    ) {
    }

    public function iterator($flags = 0)
    {
        $results = array();
        foreach (new \DirectoryIterator($this->directory) as $fileInfo) {
            $results[] = $fileInfo;
        }
        return $results;
    }

    public function readdir($flags = 0)
    {
        $results = array();
        if ($handle = opendir($this->directory)) {
            while (false !== ($filename = readdir($handle))) {
                $results[] = $filename;
            }
            closedir($handle);
        }
        return $results;
    }

    public function scandir($flags = 0)
    {
        return scandir($this->directory, $flags);
    }

    public function glob($flags = 0)
    {
        return glob($this->directory . '/*', $flags);
    }
}

if (empty($argv[1])) {
    echo "Usage: php " . basename(__FILE__) . " <directory>\n";
    exit(1);
}

$directory = $argv[1];
if (is_dir($directory)) {
    echo "Directory exists already\n";
    exit(1);
}

mkdir($directory, 0755);

$reader = new DirectoryReader($directory);
$n_iterations = 100;
$step_size = 10;
$tests = [
    ['iterator', 0],
    ['readdir', 0],
    ['scandir', 0],
    ['scandir', SCANDIR_SORT_NONE],
    ['glob', 0],
    ['glob', GLOB_NOSORT],
];

echo "files\t" . join("\t", array_map(function ($t) {
    $name = $t[0];
    if ($t[1]) {
        $name .= ' unsorted';
    }
    return $name;
}, $tests)) . "\n";

for ($n_files = 0; $n_files <= 10000; $n_files += $step_size) {
    $line = "{$n_files}\t";
    foreach ($tests as [$method, $flags]) {
        $start = microtime(true);
        for ($i = 0; $i < $n_iterations; $i++) {
            $reader->{$method}($flags);
        }
        $end = microtime(true);
        $time_per_it = sprintf('%.3f', ($end - $start) / $n_iterations * 1000);
        $line .= "$time_per_it\t";
    }
    $line .= "\n";
    echo $line;

    // increase step size after 100 iterations
    if ($n_files == 100) {
        $step_size = 100;
    } elseif ($n_files == 1000) {
        $step_size = 1000;
    }

    // add $step_size files to dir
    for ($f = $n_files; $f < $n_files + $step_size; $f += 1) {
        touch("{$directory}/{$f}.txt");
    }
}

```
