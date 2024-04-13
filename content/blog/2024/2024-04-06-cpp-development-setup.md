+++
title = "C++ development setup in 2024"
+++

I've been doing a lot of C and C++ programming in the last year and after trying
a myriad of editors and related tooling, it seems I've finally settled on a
satisfactory set-up that is both performant and reliable.

This post is mostly me praising that editor but also showcasing some of its capabilities.

## Editor

The two most popular options right now are probably VSCode and CLion, yet I found neither of them performant or reliable enough for my taste. Instead, I am using good ol' [Sublime Text 4](https://www.sublimetext.com/) in combination with [their Language Server Protocol implementation](https://lsp.sublimetext.io/) and [Clangd](https://github.com/sublimelsp/LSP-clangd).

![Sublime Text Editor in dark mode with Clangd as language server](/media/2024/sublime-text-clangd.png)

Why Sublime, you ask?

- It's an order of magnitude faster than both Clion and VSCode.
- It doesn't crash, ever.
- It's easy to configure by modifying some JSON config files.
- It's not trying to shove AI down my throat.
- It works without having to spend hours configuring it. Just install **LSP** and **LSP-Clangd** through Package Control and you have a powerful editor ready to go.
- It's very resource efficient. I can have a large C++ project open while it consumes less than 600 MB of RAM and CPU is idling.

The slight bummer is that it's not open source. Still, I really like how a small team from Australia can sit down and build a small but profitable business around a code editor.

The [community for plugins](https://packagecontrol.io/) is not as vibrant or active as it once was, but for me personally, everything I need is there.

## Compiler (flags)

As my workstation is running [Debian](https://www.debian.org/) (stable), I tend to compile using whatever version of [GCC](https://gcc.gnu.org/) and [Clang](https://clang.llvm.org/) is in the official package repositories.

Currently, this means gcc 13 and clang 14, both of which have near complete support for `-std=c++20` [^1] [^2].

When I need a newer compiler, there's always [building GCC from source](https://gcc.gnu.org/wiki/InstallingGCC) or [LLVM's APT repositories](https://apt.llvm.org/).

Both GCC and Clang are conservative with warnings, so we should enable (some of) them explicitly. The group of warnings from `-Wall` and `-Wextra` is what I always enable as a minimum.

In terms of compilation profiles, I tend to use just three:

### Development

This mode cares mostly about fast compilation times. The `LDFLAGS` environment value is set to instruct our compiler to use [mold](https://github.com/rui314/mold) for the linking stage.

```sh
CCFLAGS="-std=c11 -Wall -Wextra -Wvla -Wformat -Wformat=2 -Wconversion"
CXXFLAGS="-std=c++20 -Wall -Wextra"
LDFLAGS="-fuse-ld=mold"
```

### Debug

In this mode we want debug symbols, stack traces and runtime checks from both Address Sanitizer and Undefined Behavior Sanitizer.

```sh
CCFLAGS="-g -fsanitize=address,undefined"
CXXFLAGS="-g -fsanitize=address,undefined -D_GLIBCXX_ASSERTIONS"
ASAN_OPTIONS="strict_string_checks=1:strict_memcmp=1:quarantine_size_mb=512:detect_stack_use_after_return=1:check_initialization_order=1"
UBSAN_OPTIONS="print_stacktrace=1"
```

### Release

In this mode we want the compiler to produce the fastest code possible at the cost of longer compilation times.

```sh
CCFLAGS="-O3"
CXXFLAGS="-O3"
```

If you don't care about portability, you could add `-march=native` and `-mtune=native`.

## Diagnostics

Since we are using [Clangd](https://clangd.llvm.org/) as our Language Server, we can instruct it to emit all sorts of diagnostics (besides just compiler warnings) through [clang-tidy](https://clang.llvm.org/extra/clang-tidy/).

![clang-tidy diagnostics in Sublime Text](/media/2024/sublime-text-clangd.png)

clang-tidy is disabled by default, but we can enable it by modifying the settings for the LSP-clangd plugin.

1. Go to **Preferences > Package Settings > LSP > Servers > Clangd**.
1. Ensure `initialiationOptions.clangd["clang-tidy"]` is set to `true`:
	```json
	// Settings in here override those in "LSP-clangd/LSP-clangd.sublime-settings"
	{
	  "binary": "clangd-18",
	  "initializationOptions": {
	    "clangd.clang-tidy": true,
	    "clangd.background-index": true,
	    "clangd.header-insertion": "iwyu",
	    "clangd.completion-style": "detailed",
	  }
	}
	```
1. In your project root, create a `.clangd` file and enable/disable the [clang-tidy checks](https://clang.llvm.org/extra/clang-tidy/checks/list.html) you want. I mostly stick to the ones from `performance-*` and `cppcoreguidelines-*`.
	```yaml
	Diagnostics:
	  ClangTidy:
	    Add: [ "performance-*" ]
	```

## Formatter

We can use [clang-format](https://clang.llvm.org/docs/ClangFormat.html) through Sublime's LSP-clangd plugin.

Go to **Preferences > Package Settings > LSP > Settings** and ensure `lsp_format_on_save` is set to `true`.

Your code style can be configured through a `.clang-format` YAML file in your project root.

## Profiling

To find performance bottlenecks, I've not come across anything that beats [perf](https://perf.wiki.kernel.org/index.php/Main_Page) + [flamegraph.pl](https://github.com/brendangregg/FlameGraph).

The author of the latter tool has a great post on his blog with many language specific tips on how to best [create flamegraphs from a perf report](https://www.brendangregg.com/FlameGraphs/cpuflamegraphs.html).


[^1]: https://gcc.gnu.org/projects/cxx-status.html#cxx20
[^2]: https://clang.llvm.org/cxx_status.html
