+++
title = "Writing a bytecode interpreter - in C"
date = 2020-03-06
+++

Some time during 2016 I got my hands on the book [Writing an interpreter in Go](https://interpreterbook.com/) by [Thorsten Ball](https://thorstenball.com/). I skimmed through the first few chapters, liked what I read and then... life happened and I never actually got around to building an interpreter. :(

Until last month, that is. I cleared out my schedule and finally started going through the book. 

For double the fun, I picked C as my programming language of choice instead of Go. This turned out to be a great decision as it really forced me to understand what was going on, instead of always having the easy option of just copying the author's code.

I codenamed my implementation [Monkey-C Monkey-Do](https://github.com/dannyvankooten/monkey-c-monkey-do). 

The book takes you through all the stages to get an interpreter for the [Monkey programming language](https://monkeylang.org/) up and running:

- Tokenize the input
- Parse the tokens into an Abstract Syntax Tree (AST)
- Evaluate the tree

This tree-walking evaluator needs about 6 seconds to calculate the 35th fibonacci number using [a very sub-optimal algorithm with lots of recursion](https://github.com/dannyvankooten/monkey-c-monkey-do/blob/946311e77d33d584e6fcfd9f87d0199242973947/examples/fib35.monkey). That is certainly not bad, but it's also not great compared to any of today's production-grade interpreted languages. 

For comparison, on the same machine Python 3.7 needs 2.3 seconds for that exact same algorithm and Node 15 only needs a whopping 200 miliseconds (due to its JIT compilation).

Can we do better, without talking to the hardware directly?

### Writing a bytecode compiler and virtual machine

Luckily, the author of the book didn't stop there. In his second book ([Writing a compiler in Go](https://compilerbook.com/)) the author walks you through the steps of building a bytecode interpreter. 

Reusing the AST from the first book, it shows you how to build a compiler outputting bytecode. Simultaneously, you start building a virtual machine capable of executing that bytecode.

After getting the virtual machine up and running, calculating the 35th fibonacci number now only takes 0.82 seconds. That's much closer or even faster than some other interpreted languages already.

I wholeheartedly recommend the two books. Not only is it a lot of fun to write your own interpreter, it also cleared up a lot of the magic for me surrounding how interpreters actually work and what happens behind the scene when a program is evaluated by an interpreter.

### Programming in C

This was my first experience programming in C and it surprised me to discover that I really enjoyed using it. Because of the small language specification very little time was spent reading documentation. 

That's not to say I did not shoot myself in the foot a good few times or struggled with memory management. Using C really cemented my understanding of some of the languages that came after it though. And what problems they attempt to solve or improve upon over C.

Given the right tooling, I've grown quite fond of C... Sorry, not sorry.


#### Resources 

- Book: [The C Programming Language](https://en.wikipedia.org/wiki/The_C_Programming_Language) by Kernighan & Ritchie. Still the best resource for learning C.
- Tools: [Valgrind](https://valgrind.org/), [Gprof](https://sourceware.org/binutils/docs/gprof/), [GNU make](https://www.gnu.org/software/make/manual/make.html), [GNU Debugger](https://www.gnu.org/software/gdb/)
- [This comment in the CPython source](https://github.com/python/cpython/blob/master/Python/ceval.c#L775) explaining the use of computed GOTO's in the VM for a performance gain due to better CPU branch prediction over using a switch statement.
- [Memory allocation strategies](https://www.gingerbill.org/series/memory-allocation-strategies/) by Ginger Bill.