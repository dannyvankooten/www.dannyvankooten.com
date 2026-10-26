---
title: "Writing a bytecode interpreter – in C"
description: "Building a bytecode interpreter and virtual machine in C for the Monkey programming language."
date: "2020-03-06T00:00:00+00:00"
modified: "2026-05-14T20:22:59+02:00"
categories: ["Programming"]
---

Some time during 2016, I got my hands on [Writing an Interpreter in Go](https://interpreterbook.com/) by [Thorsten Ball](https://thorstenball.com/). I skimmed the first few chapters, liked what I read, and then life happened. I never got around to building an interpreter.

Until last month. I cleared out my schedule and finally started working through the book.

For double the fun, I chose C as my programming language instead of Go. That turned out to be a great decision because it forced me to understand what was going on instead of falling back to copying the author’s code.

I codenamed my implementation [Monkey-C Monkey-Do](https://github.com/dannyvankooten/monkey-c-monkey-do).

The book takes you through all the stages needed to get an interpreter for the [Monkey programming language](https://monkeylang.org/) up and running:

-   Tokenize the input
-   Parse the tokens into an Abstract Syntax Tree (AST)
-   Evaluate the tree

This tree-walking evaluator needs about 6 seconds to calculate the 35th Fibonacci number using [a very sub-optimal algorithm with lots of recursion](https://github.com/dannyvankooten/monkey-c-monkey-do/blob/946311e77d33d584e6fcfd9f87d0199242973947/examples/fib35.monkey). That is not bad, but it is also not great compared to today’s production-grade interpreted languages.

For comparison, on the same machine, Python 3.7 needs 2.3 seconds for that exact same algorithm, while Node 15 only needs a whopping 200 milliseconds because of its JIT compilation.

Can we do better without talking to the hardware directly?

### Writing a bytecode compiler and virtual machine

Luckily, the author did not stop there. In his second book, [Writing a Compiler in Go](https://compilerbook.com/), Thorsten Ball walks you through the steps of building a bytecode interpreter.

Reusing the AST from the first book, you build a compiler that outputs bytecode. At the same time, you build a virtual machine that can execute that bytecode.

After getting the virtual machine up and running, calculating the 35th Fibonacci number now only takes 0.82 seconds. That is much closer to, and already faster than, some other interpreted languages.

I wholeheartedly recommend the two books. Writing your own interpreter is a lot of fun, and it also cleared up much of the magic around how interpreters work and what happens behind the scenes when an interpreter evaluates a program.

### Programming in C

This was my first experience programming in C, and I was surprised by how much I enjoyed it. Because the language specification is small, I spent very little time reading documentation.

That does not mean I avoided shooting myself in the foot a few times or struggling with memory management. But using C cemented my understanding of some of the languages that came after it, and of the problems they attempt to solve or improve upon.

Given the right tooling, I have grown quite fond of C. Sorry, not sorry.

#### Resources

-   Book: [The C Programming Language](https://en.wikipedia.org/wiki/The_C_Programming_Language) by Kernighan & Ritchie. Still the best resource for learning C.
-   Tools: [Valgrind](https://valgrind.org/), [Gprof](https://sourceware.org/binutils/docs/gprof/), [GNU make](https://www.gnu.org/software/make/manual/make.html), [GNU Debugger](https://www.gnu.org/software/gdb/)
-   [This comment in the CPython source](https://github.com/python/cpython/blob/master/Python/ceval.c#L775) explaining the use of computed GOTO’s in the VM for a performance gain due to better CPU branch prediction over using a switch statement.
-   [Memory allocation strategies](https://www.gingerbill.org/series/memory-allocation-strategies/) by Ginger Bill.
