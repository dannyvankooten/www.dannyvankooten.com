---
layout: post
title: 'Writing an interpreter and compiler in C'
date: '2020-03-06'
tags:
- interpreter
- compiler
- bytecode
---

Some time during 2016 I got my hands on [Writing an interpreter in Go](https://thorstenball.com/) by [Thorsten Ball](https://thorstenball.com/). I skimmed through the first few chapters, really liked what I read and then life happened and I never actually got around to building an interpreter.

Until last month. I cleared out my schedule and finally started going through the book. For double the fun, I picked C as my language of choice.

After finishing the book, I had an interpreter (codenamed [Monkey-C Monkey-Do](https://github.com/dannyvankooten/monkey-c-monkey-do)) capable of running the [Monkey programming language](https://monkeylang.org/).

The interpreter calculates the 35th fibonacci number in about 6 seconds on my machine. Which is certainly not bad given its simple tree-walking approach, but also not great. For reference, Python needs 2.3 seconds and Node only needs a whopping 200 miliseconds.

### Writing a bytecode compiler and virtual machine

That's where the second book comes in: [Writing a compiler in Go](https://compilerbook.com/). Reusing the lexer and parser of your interpreter, it shows you how to build a compiler outputting bytecode. Simultaneously, you start building a virtual machine capable of executing that bytecode.

After getting the virtual machine up and running, calculating the 35th fibonacci number now only takes 1.16 seconds. That's much closer to some of today's production-grade interpreted languages!

More importantly, some of the magic surrounding interpreters and compilers has been cleared up for me. I highly recommend these two books for anyone with some time and interest in how interpreted languages usually work. 

Unrelated to the actual books was my surprise at how elegant the C programming language  is. It's so small and simple, yet so incredibly powerful.

#### Resources 

- [The C Programming Language](https://en.wikipedia.org/wiki/The_C_Programming_Language) by Kernighan & Ritchie. Still the best resource for learning C.
- [Memory allocation strategies](https://www.gingerbill.org/series/memory-allocation-strategies/) by Ginger Bill.
- [Comment in the CPython source](https://github.com/python/cpython/blob/master/Python/ceval.c#L775) explaining the use of computed GOTO's for a performance gain.
- "[Performance Matters](https://www.youtube.com/watch?v=r-TLSBdHe1A)", a talk by Emery Berger. This helped explain some weird performance regressions I ran into with changes that should really have made my code faster in theory, but didn't.
