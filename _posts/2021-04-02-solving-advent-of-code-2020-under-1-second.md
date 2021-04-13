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

For this last goal I was inspired by [Tim Visee](https://timvisee.com/blog/solving-aoc-2020-in-under-a-second/) who did a really great write-up of some of the tricks he used to efficiently solve this year's challenges. It sounded like a really fun thing to do and I was already well underway for such a thing anyway. 

Two weeks later, iet ies done! Total runtime is 880 ms on my laptop, so I'm quite pleased with the results. 

I could probably squeeze out a few more miliseconds here and there, but I see no options for making the 2 bottlenecks ([day 15](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/15.c) and [day 23](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/23.c)) run any faster (except for throwing more hardware at it).

The code is on GitHub here: [dannyvankooten/advent-of-code-2020](https://github.com/dannyvankooten/advent-of-code-2020)

To be honest, finishing all challenges was harder than getting them all to run in under a second. I really enjoy optimising code for performance and trying out different algorithms.

**Things I learned:**

- You can represent a [hexagonal grid](https://www.redblobgames.com/grids/hexagons/) in a 2D array by simplify shifting every odd column or row ([day 24](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/24.c)). 
- [Linear probing](https://en.wikipedia.org/wiki/Linear_probing) is a much simpler way to deal with hash collissions than a linked list and results in less cache misses because the values can reside in contiguous memory locations. 
- To check neighbors or directions in a 2D grid, it's a lot more concise to keep an array of `Δx` and `Δy` values versus writing out all the various directions in a separate loop.
- You can't brute force your way out of everything. Sometimes, math is required to get decent performance. Specifically, [Chinese Remainder Theorem](https://en.wikipedia.org/wiki/Chinese_remainder_theorem) for [day 13](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/13.c) and any of the [algorithms for finding the discrete log](https://en.wikipedia.org/wiki/Baby-step_giant-step) for [day 25](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/25.c).
- Tooling! I wouldn't want to write C without [Valgrind](https://valgrind.org/) and [Gprof](https://sourceware.org/binutils/docs/gprof/index.html). [Cachegrind](https://valgrind.org/docs/manual/cg-manual.html) can be useful too.
- When an array gets really sparse, it can be more efficient to use a hashmap despite the added overhead ([day 15](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/15.c)).
- In loops, it can be really useful to add a call to `getc(stdin)` combined with `printf` debugging to allow stepping through the loop. Especially if you haven't yet taken the time to learn [GDB](https://www.gnu.org/software/gdb/) well enough, like me.


--- 

**[Day 1](https://adventofcode.com/2020/day/1)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/01.c) / runtime: 13 μs

The task was to find the product of the three entries in the puzzle input that sum to 2020. Since most numbers in the input were well over half that, it made sense to first sort the input in ascending order before starting our loops. 

----

**[Day 2](https://adventofcode.com/2020/day/2)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/02.c) / runtime: 12 μs

Day 2 was fairly straightforward, so I won't go into any details on it.

----

**[Day 3](https://adventofcode.com/2020/day/3)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/03.c) / runtime: 11 μs

The puzzle input is a 2D grid of tree positions. We're then tasked with counting the number of trees for given slopes. I just looped over the 2D array multiple times, each time incrementing the row- and column indices with the given slopes.

---

**[Day 4](https://adventofcode.com/2020/day/4)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/04.c) / runtime: 36 μs

The input consisted of several "passports" with their field names and values in a random order. Each field had restrictions on what a valid value for that field looked like. 

My solution iterates over each passport in the input, marks each field (except the one that was to be ignored) as valid (1) or invalid (0) in an array and then uses `memcmp` to check whether the passport is valid or not.

There is a possible optimization by skipping forward to the next passport whenever any of the required fields is invalid, but since the runtime is already so low I did not find this worth the time.

---

**[Day 5](https://adventofcode.com/2020/day/5)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/05.c) / runtime: 24 μs

My solution decodes each input line into a row and column, turns these into a seat ID and finds the highest seat ID. At the same tame it toggles a boolean value in a 2D array to keep track of all occupied seats.

It then iterates over this array while skipping the first few rows to find the first seat that is empty. 

---

**[Day 6](https://adventofcode.com/2020/day/6)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/06.c) / runtime: 32 μs


For day 6 I create two arrays of size 26 to keep track of user answers and group answers respectively. At the end of each line I `AND` the two arrays, so I have an array filled with `1`'s for the answers that were answered by every user in a group. Counting the `1` values in the group answers array gets us the number of questions answered by everyone in a group.

---

**[Day 7](https://adventofcode.com/2020/day/7)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/07.c) / runtime: 4144 μs

Day 7 was the ideal candidate for a hashmap, since we have to do a ton of lookups by the name of a bag. Since I had just read Ben Hoyt's post on [how to implement a hash table in C](https://benhoyt.com/writings/hash-table-in-c/), I decided to give his implementation a try. 

Sadly I don't have the linear search version in version control, as I would like to see what difference it made, but IIRC it was huge given that there are 594 bags in my input.

---

**[Day 8](https://adventofcode.com/2020/day/8)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/08.c) / runtime: 105 μs

Day 8 reminded me of the [bytecode interpreter I wrote last year](https://github.com/dannyvankooten/monkey-c-monkey-do), so I really enjoyed this one. To detect the infinite loop I kept changing a single JUMP instruction to a NOOP until we reached the end of the program without repeating an instruction.

---

**[Day 10](https://adventofcode.com/2020/day/10)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/10.c) / runtime: 8 μs

Dynamic programming! It took me a while to realise this though. For part 2 I go over a sorted array of adapter joltages and then count how many of the previous adapters it can connect to, adding the sum of options to get to that previous adapter to the one we're looking at.

---

**[Day 11](https://adventofcode.com/2020/day/11)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/11.c) / runtime: 3186 μs

A 2D square-grid problem where we have to look at all 8 neighbors for every point. I optimized this solution by keeping a list of neighbor indices for each seat, so these do not have to be recomputed on every transmutation.

Another optimization is to keep a list of seats to check and remove a seat from this list once it reached its permanent state. If a seat is occupied and has less than 5 occupied neighbors, it is permanently occupied. A seat is permanently empty if it has a permanently occupied neighboring seat. 

---

**[Day 12](https://adventofcode.com/2020/day/12)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/12.c) / runtime: 79 μs

A ship that moves towards a waypoint that is positioned relative to itself according to instructions in the puzzle input. I didn't optimize this solution that much since the straightforward approach was already plenty fast. 

I used `sin()` and `cos()` for [rotating](https://en.wikipedia.org/wiki/Rotation_matrix) the waypoint, but since the rotation amount is fixed to a multiple of `90` I should probably get rid of those.

---

**[Day 13](https://adventofcode.com/2020/day/13)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/13.c) / runtime: 4 μs

This day required the [Chinese Remainder Theorem](https://en.wikipedia.org/wiki/Chinese_remainder_theorem) to get done in a reasonable amount of time. Sadly I was not able to come up with this myself, but I saw a mention of it after getting stuck on my brute-force approach.

---

**[Day 14](https://adventofcode.com/2020/day/14)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/14.c) / runtime: 1611 μs

Updating "memory addresses" with certain values from the puzzle input after applying a (changing) mask to the address. Since addresses were so large and wouldn't fit in an array, I wrote a simple hashmap with integer keys and values.

To make sure the hashed key value is within the bounds of the backing array, I made sure capacity itself was a power of 2 and then used a bitwise `&` on the capacity - 1. This is a lot faster than using the modulo operator.

---

**[Day 15](https://adventofcode.com/2020/day/15)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/15.c) / runtime: 461113 μs (461 ms)

Today would have made the 1-second goal impossible without good enough hardware and a language that compiles to machine code. The solution is fairly straightforward and doesn't leave much room for optimization.

For values lower than ~500K, I used an array to look-up the previous position of a number in constant time. 

Since values larger than 500K were further apart (sparse), I used an optimized hashmap implementation for these values to store the previous positions. It uses a really limited amount (< 10) of linear probing attempts to prevent spending too much time on values that have not been seen before.

---

**[Day 16](https://adventofcode.com/2020/day/16)** / [code](https://github.com/dannyvankooten/advent-of-code-2020/blob/main/16.c) / runtime: 360 μs 

Parse a bunch of field rules and values. We can deduce the position of each field by picking the only available option and removing it from all other fields. Repeat until we know the position for each field. 

Today's optimization was to ensure we're breaking out of each loop or skipping to the next iteration as soon as possible.