---
layout: post
title: 'Solving Advent of Code 2020 in under a second '
date: '2021-04-02'
tags:
- advent of code
- C
---

[Advent of Code](https://adventofcode.com/) is an annual event of small programming puzzles for a variety of skill sets and skill levels that can be solved in any programming language. 

Last year (AoC 2019), I participated for the first time and used Rust as my language of choice.

This year, albeit a few months after the event actually occurred, I participated again and used C as my weapon of choice. (And yes, I did hurt myself in the process.)

I set out with two goals in mind:

- To finish all challenges within a single month.
- To solve them all in under 1 second of runtime (on a single CPU core).

For this last goal I was inspired by [Tim Visee](https://timvisee.com/blog/solving-aoc-2020-in-under-a-second/). It sounded like a really fun thing to do and I was already well underway for such a thing anyway. 

Two weeks later, iet ies done! All solutions combined run in 880 ms on my laptop, so I'm quite pleased with the results. 

The code is on GitHub here: [dannyvankooten/advent-of-code-2020](https://github.com/dannyvankooten/advent-of-code-2020)

To be honest, finishing all challenges was harder than getting them all to run in under a second, as I really enjoy optimising code for performance and trying out different algorithms.

So, what did it take and what did I learn?

- Cache misses are expensive, so (contiguous) memory layout is  important.
- Preallocate all the things.
- Array lookups or alternatively hashmaps are your friends. Linear time complexity is not.
- Don't forget `-Ofast` and `-march=native` as [optimization flags](https://gcc.gnu.org/onlinedocs/gcc/Optimize-Options.html) for your compiler.
- You can't brute force your way out of everything. Sometimes,  math is required. Looking at you, day 13 and [Chinese Remainder Theorem](https://en.wikipedia.org/wiki/Chinese_remainder_theorem).
- Tooling! I wouldn't want to write C without [Valgrind](https://valgrind.org/) and [Gprof](https://sourceware.org/binutils/docs/gprof/index.html). [Cachegrind](https://valgrind.org/docs/manual/cg-manual.html) can be useful too.
- You can represent a [hexagonal grid](https://www.redblobgames.com/grids/hexagons/) in a 2D array by simplify shifting every odd column or row. 
- [Linear probing](https://en.wikipedia.org/wiki/Linear_probing) is simpler a simpler way to deal with hash collissions than using a linked list and results in less cache misses too. Still, I miss [std::collections::HashMap](https://doc.rust-lang.org/std/collections/struct.HashMap.html).