+++
date = 2022-11-17 
title = "(Re)writing an interpreter in Rust"
+++

Two years ago I [wrote my first interpreter](@/blog/2020/2020-03-06-writing-an-interpreter-compiler.md) for a toy programming language called Monkey, in C.

The thing works and is pretty fast, but I remember a lot of frustration dealing with segfaults or hard-to-track down memory leaks as soon as I introduced heap allocated values. 

Much of this is undoubtedly due to me not being very experienced with C. But, and I know this for a fact now, it's also because C makes it very easy for issues like this to pop-up at all.

## Rewrite it in Rust

One of my long-time friends has been working hard on a [fast multi-threaded DataFrame library in Rust](https://www.pola.rs/). I wanted to contribute something so started working on a [CLI interface](https://github.com/pola-rs/polars/pull/5175) for it. 

Working my way through the codebase made me realise that if I wanted to contribute in a more significant way, I had to first work on my Rust skills. I was already comfortable enough with Rust to solve [Advent of Code](https://github.com/dannyvankooten/advent-of-code/tree/main/2019) puzzles, but I had yet to really get in a good fight with the borrow checker. I needed a bigger project to really get rusty.

What better way to practice than to build an interpreter? It's fun and you can really go crazy trying to make it fast. Shall we try to make it at least as performant as the one I wrote in C? 

As our benchmark program, we'll be measuring a (very inefficient) recursive fibonacci:

```
function fib(n) {
    if n < 2 {
        return n;
    }

    return fib(n-1) + fib(n-2);
}

fib(35)
```

Let's take a quick look at the times to beat (using [Hyperfine](https://github.com/sharkdp/hyperfine)):

```
hyperfine \
    -n pepper-tree-walker "pepper --tree-walker fib35.pr" \
    -n pepper-vm "pepper --vm fib35.pr" \
    -n python-3.10 "python fib35.py"  \
    --runs 10 --export-markdown /tmp/hf.md && cat /tmp/hf.md
```


<div style="overflow-x: scroll;">

| Command | Mean [s] | Min [s] | Max [s] | Relative |
|:---|---:|---:|---:|---:|
| `pepper-tree-walker` | 3.791 ± 0.031 | 3.755 | 3.816 | 5.00 ± 0.13 |
| `pepper-vm` | 0.758 ± 0.018 | 0.738 | 0.773 | 1.00 |
| `python-3.10` | 2.033 ± 0.008 | 2.025 | 2.041 | 2.68 ± 0.07 |

</div>

3.8 seconds for a tree-walking implementation or under 1 second when compiled to bytecode and executed inside a virtual machine. Also included is CPython 3.10, which also uses bytecode but is not known to be particularly fast.

Now, let's see if we can beat these times using Rust.

### First benchmarks using a tree-walking interpreter

Fast forward some time, we now have a first draft of a working interpreter that successfully lexes, parses and evaluates the mentioned fibonacci program. It's called [Nederlang](https://github.com/dannyvankooten/nederlang) and you can [try it in your browser here](https://dannyvankooten.github.io/nederlang/playground/). Thanks WASM!

We'll be starting our optimization work from [this commit](https://github.com/dannyvankooten/nederlang/commit/873a737bfa22d1222e2904aa52f6386175250f87).

The source code is first turned into tokens and then parsed into an Abstract Syntax Tree (AST), where each node is a variant of the `Expr` enum:

```rust
enum Expr {
    Int(ExprInt),
    Float(ExprFloat),
    Bool(ExprBool),
    String(ExprString),
    Infix(ExprInfix),
    Prefix(ExprPrefix),
    If(ExprIf),
    Identifier(String),
    Function(ExprFunction),
    Call(ExprCall),
    Assign(ExprAssign),
    Declare(ExprDeclare),
    Block(Vec<Expr>),
}

struct ExprInt {
    value: i64,
}

struct ExprInfix {
    left: Box<Expr>,
    operator: Operator,
    right: Box<Expr>,
}

// etc..
```

Then upon running the program, we'll walk this tree and evaluate each expression. Some of these expressions mutate variables stored inside the `Environment` type:

```rust
struct Environment<'a> {
    symbols: RefCell<HashMap<String, Object>>,
    outer: Option<&'a Environment<'a>>,
}
```

When resolving a variable by its name, we look up the name in the `symbols` HashMap, traversing upwards to the outermost `Environment` until we have a match. 

<div id="aliases"></div>

Before we start our work, let's define some useful aliases for the current shell session so we don't have to deal with typing out the same commands over and over again.

```bash
alias fb="cargo build --release && hyperfine --runs 10 'target/release/nederlang fib.nl' --export-markdown /tmp/hf.md && cat /tmp/hf.md"
alias fp="cargo build --release && perf record --call-graph dwarf  target/release/nederlang fib.nl && perf report"
alias ff="cargo flamegraph -- target/release/nederlang fib.nl"
alias fc="perf stat -e task-clock,cycles,instructions,cache-references,cache-misses target/release/nederlang fib.nl"
```

Now we're ready to run a very first benchmark.... **~drum roll~**

```
fb
```

_(Note: `fb` is the name of one of [the aliases defined here](#aliases).)_

<div style="overflow-x: scroll;">

| Command | Mean [s] | Min [s] | Max [s] |
|:---|---:|---:|---:|
| `nederlang fib.nl` | 39.292 ± 0.309 | 39.037 | 39.636 |

</div>

**39 seconds**. Ouch. I know tree walking is not supposed to be fast, but given the C implementation manages to do it in under 5 seconds, surely we should be able to come close to that in Rust?

### Optimizing Rust code for performance

Let's take a look at where all this time is spent by running `fp` (another one of [the aliases defined here](#aliases), which simply calls into `perf` to generate a call graph):

```
100.00%     8.34%   [.] nederlang::eval::eval_expr
100.00%     0.00%   [.] nederlang::eval::eval_infix_expr (inlined)
 99.94%     2.10%   [.] nederlang::eval::eval_block
 99.69%     0.00%   [.] nederlang::eval::eval_if_expr (inlined)
 59.32%    15.26%   [.] nederlang::eval::Environment::resolve
 27.53%     0.00%   [.] std::collections::hash::map::HashMap<K,V,S>::get (inlined)
 27.53%     0.00%   [.] hashbrown::map::HashMap<K,V,S,A>::get (inlined)
 27.49%     0.00%   [.] hashbrown::map::HashMap<K,V,S,A>::get_inner (inlined)
```

Right away it shows that a whopping 59% of time is spent in `Environment::resolve`, which resolves variables by their name. Let's optimize that.

#### Using a faster HashMap implementation

What if we switch to a faster (but not cryptographically secure) HashMap implementation like [fxhash](https://crates.io/crates/fxhash)?

We add the dependency to `Cargo.toml`:

```
[dependencies]
fxhash = "0.2.1"
```

And then import the new HashMap type under an alias, so the rest of our code can remain untouched:

```rust
use fxhash::FxHashMap as HashMap;
```

Let's run another benchmark (using the `fb` alias) to see what Hyperfine thinks about this change:

<div style="overflow-x: scroll;">

| Command | Mean [s] | Min [s] | Max [s] | 
|:---|---:|---:|---:|
| `nederlang fib.nl` | 32.284 ± 0.337 | 31.953 | 32.627 |

</div>

Down to **32 seconds**, a 18% performance improvement. Not bad, but still slow... We need to rethink the way we store and resolve variables.

#### Using a Vec instead of a Hashmap 

In Nederlang there exists a global scope and a local scope. Each function call creates a new local scope so that every variable declared inside that function is dropped once the function returns. 

We could represent that as a `Vec<HashMap<String, Object>>`, pushing a new HashMap before evaluating a function body and then popping it afterwards.

But also, in a typical program there will only be a handful of variables per scope. What if we drop the `HashMap` entirely and use a `Vec<Vec<(String, Object)>>` instead? 

The look-up time will be **_O(n)_** instead of **_O(1)_** but since there are only a handful of variables to iterate over, I have a feeling it will be faster than going through a hash function.

```rust
type Scope = Vec<(String, Object)>;

struct Environment {
    scopes: Vec<Scope>,
}

impl Environment {
    fn resolve(&self, ident: &str) -> Object {
        for scope in self.scopes.iter().rev() {
            for (name, value) in scope {
                if name == ident {
                    return value.clone();
                }
            }
        }
      
        Object::Null
    }
}
```

You can see the entire [commit here](https://github.com/dannyvankooten/nederlang/commit/c25a55d1d8138b9d472fcab4fd9cf3ca2b7aee04). Let's see what this change does to our benchmark times.

<div style="overflow-x: scroll;">

| Command | Mean [s] | Min [s] | Max [s] | 
|:---|---:|---:|---:|
| `nederlang fib.nl` | 23.883 ± 0.129 | 23.743 | 23.998 |

</div>

**24 seconds**. a 25% performance improvement! Just 80% more to go... 


#### Separating names from their values

```
100.00%    17.41%  [.] nederlang::eval::eval_expr                                                                 
100.00%     0.00%  [.] nederlang::eval::eval_infix_expr (inlined)                                                 
100.00%     4.24%  [.] nederlang::eval::eval_block                                                                
100.00%     0.00%  [.] nederlang::eval::eval_if_expr (inlined)                                                    
 47.63%     5.58%  [.] nederlang::eval::Environment::resolve     
```

48% of the time is still spent inside `Environment::resolve`. What else can we do to speed this up?

To resolve a variable value by its name, it is currently iterating over a bunch of separately allocated `Vec` instances holding a tuple consisting of the variable name and the value. 

Changing the language specificiation of Nederlang to enforce that function names can not be shadowed would allow us to speed-up resolving functions by their name. That way we can start by looking at the outer-most (global) scope and only then start traversing all of the inner scopes. A cool trick, but not really Rust related so let's think of what else there is.

What about using a single `Vec<(String, Object)>` then? If we store the number of declared variables before evaluating the function body, we can then call [Vec::truncate()](https://doc.rust-lang.org/std/vec/struct.Vec.html#method.truncate) afterwards to get rid of all variables declared in that function. 

While we're at it, let's use a separate `Vec` for `names` and `values`. That should speed-up resolving variables by their name since we're really only interested in the value of the variable we're looking for. 

```rust
struct Environment {
    names: Vec<String>,
    values: Vec<Object>
}

impl Environment {
    fn new() -> Self {
        Environment {
            names: Vec::with_capacity(64),
            values: Vec::with_capacity(64)
        }
    }

    fn resolve(&self, ident: &str) -> Object {
        if let Some(pos) = self.names.iter().rev().position(|name| *name == ident) {
            return self.values[self.values.len() - 1 - pos].clone();
        }
        
        Object::Null
    }

    fn insert(&mut self, ident: String, value: Object) {
        self.names.push(ident);
        self.values.push(value);
    }
}
```

A nice benefit is that we can now start pushing values into the environment while still evaluating the argument expressions. This works because without a corresponding element in the `names` vector, they won't be resolved in case an argument expressions refers to a variable with the same name (from another scope).

```rust
// In eval_call_expr(...)
let var_length = env.names.len();
for value_expr in &expr.arguments {
    let value = eval_expr(value_expr, env)?;
    env.values.push(value);
}
for name in parameters {
    env.names.push(name);
}

let result = eval_block(&body, env);
env.names.truncate(var_length);
env.values.truncate(var_length);
return result;
```

Are we faster than the C implementation yet? 

<div style="overflow-x: scroll;">

| Command | Mean [s] | Min [s] | Max [s] |
|:---|---:|---:|---:|
| `nederlang fib.nl` | 21.415 ± 0.112 | 21.308 | 21.578 |

</div>

And the profile provided by `perf`:

```
100.00%    16.80%   [.] nederlang::eval::eval_expr
100.00%     0.00%   [.] nederlang::eval::eval_infix_expr (inlined)
100.00%     4.73%   [.] nederlang::eval::eval_block
100.00%     0.00%   [.] nederlang::eval::eval_if_expr (inlined)
 49.90%     4.84%   [.] nederlang::eval::Environment::resolve
 46.16%     0.00%   [.] <nederlang::object::Object as core::clone::Clone>::clone (inlined)
 41.64%     5.54%   [.] <alloc::vec::Vec<T,A> as core::clone::Clone>::clone
 41.51%     0.00%   [.] alloc::slice::<impl [T]>::to_vec_in (inlined)
 41.51%     0.00%   [.] alloc::slice::hack::to_vec (inlined)
 41.51%     0.00%   [.] <T as alloc::slice::hack::ConvertVec>::to_vec (inlined)
 40.23%    19.38%   [.] <nederlang::ast::Expr as core::clone::Clone>::clone
```

The good news is that we're down to **21 seconds** now, so resolving variables did indeed get faster. 

The bad news is that we can now no longer ignore the glaring obvious, all these calls to `clone()`. We're cloning a bunch of `Expr` and `Vec` types inside the hot path.

#### Reference nodes in the AST vs. cloning

The reason is that Nederlang's object type looks like this:

```rust
enum Object {
    Null,
    Int(i64),
    Float(f64),
    Bool(bool),
    String(String),
    Func(Vec<String>, Vec<Expr>),
}
```

The `Object::Func` variant owns two `Vec` types. These hold the parameter names and the function body. But wait, can't we just keep the AST around in memory and use that? That way we'll only have to store a reference inside our `Object::Func` variant.

Being lazy and coming from C, let's first get a feel for what kind of performance gain we can expect by simply storing a raw pointer and dereferencing it later. (Don't worry, I  promise to fix it properly later on.)

```rust
// Storing the raw pointer
enum Object {
    ...
    Func(*const ExprFunction),
}

// Creating the raw pointer
Object::Func(expr as *const ExprFunction)

// Dereferencing the raw pointer
let func = unsafe { 
    &*(ptr as *const ExprFunction) 
};
```

Here's the [full commit](https://github.com/dannyvankooten/nederlang/commit/17f76590039c88b54de58a169a2e58b2a17faca8). Let's run another benchmark now.


| Command | Mean [s] | Min [s] | Max [s] | 
|:---|---:|---:|---:|
| `nederlang fib.nl` | 4.222 ± 0.059 | 4.177 | 4.322 | 

Down to **4.2 seconds**. That's the 80% improvement we needed! Surely that is worth introducing the lifetime constraints and sleeping sound at night knowing our code is safe. 

```diff
- fn eval_expr(expr: &Expr, env: &mut Environment) -> Result<Object, Error> {
+ fn eval_expr<'a>(expr: &'a Expr, env: &mut Environment<'a>) -> Result<Object<'a>, Error>
```

1337 [lifetime constrains later](https://github.com/dannyvankooten/nederlang/commit/0d2eaeaaeb297cf4610cf53db1eba7bed59a4f85)...

Now we're making sure the AST sticks around in memory for longer than the Environment type, why not [use string references for the variable names](https://github.com/dannyvankooten/nederlang/commit/97d52c3d836e89179e7a309b502a4ce51541f957) too? We've got the lifetimes set-up now anyway and it should get rid of some String clones.

```diff
struct Environment<'a> {
-   names: Vec<String>,
+   names: Vec<&'a str>,
    values: Vec<Object<'a>>
}
```

<div style="overflow-x: scroll;">

| Command | Mean [s] | Min [s] | Max [s] | 
|:---|---:|---:|---:|
| `nederlang fib.nl` | 3.699 ± 0.015 | 3.685 | 3.720 |

</div>

Another 12% faster and now on par with the tree-walking interpreter hosted in C. Hurray! Let's keep going though, there's way more optimization to be done.

### Representing dynamically typed values

Now we've gotten rid of most allocation related performance hogs, it's about time we look at the `Object` type.

```rust
enum Object<'a> {
    Null,
    Int(i64),
    Float(f64),
    Bool(bool),
    String(String),
    Func(&'a ExprFunction),
}
```

Looks good to me. Let's see how it's laid out in memory using the [-Zprint-type-sizes flag](https://nnethercote.github.io/perf-book/type-sizes.html) for `rustc`:

```
RUSTFLAGS=-Zprint-type-sizes cargo +nightly build --release
```

Finding our `Object` type in the result:

```
print-type-size type: `object::Object<'_>`: 32 bytes, alignment: 8 bytes
print-type-size     discriminant: 1 bytes
print-type-size     variant `String`: 31 bytes
print-type-size         padding: 7 bytes
print-type-size         field `.0`: 24 bytes, alignment: 8 bytes
print-type-size     variant `Int`: 15 bytes
print-type-size         padding: 7 bytes
print-type-size         field `.0`: 8 bytes, alignment: 8 bytes
print-type-size     variant `Float`: 15 bytes
print-type-size         padding: 7 bytes
print-type-size         field `.0`: 8 bytes, alignment: 8 bytes
print-type-size     variant `Func`: 15 bytes
print-type-size         padding: 7 bytes
print-type-size         field `.0`: 8 bytes, alignment: 8 bytes
print-type-size     variant `Bool`: 1 bytes
print-type-size         field `.0`: 1 bytes
print-type-size     variant `Null`: 0 bytes
```

32 bytes?! What does the Rust reference have to say about [enumerated types](https://doc.rust-lang.org/reference/types/enum.html):

> Any enum value consumes as much memory as the largest variant for its corresponding enum type, as well as the size needed to store a discriminant.

That explains. The largest variant is `Object::String`, holding a `String` type of 24 bytes. The discriminant is taking up a single byte, but because of alignment it will add another 7 bytes of padding.  

Using 32 bytes while most of our variants could theoretically fit into just 8 bytes does not sound optimal. What can we do to shrink it?

One option is to `Box` the value of the `Object::String` variant, but that shrinks it to 16 bytes. Can we somehow make the value + a type discriminant fit in 8 bytes?

Sure! We can do [pointer tagging](https://www.npopov.com/2012/02/02/Pointer-magic-for-efficient-dynamic-value-representations.html) in Rust.


#### Shrinking our Object type using pointer tagging

> Trigger warning: unsafe Rust ahead!

Right now our Object type owns the `String` value it holds, which means a lot of cloning just passing objects (of this variant) around. 

We should probably allocate our String objects elsewhere and have our `Object` store a reference (or pointer) instead. That also means we need some kind of garbage collection to manage that memory for us, but I will (happily) ignore that for this post as we're not really working with any heap allocated values in our recursive fibonnacci program anyway.

What if we get our `Object` type to look something like this?

```rust
enum Object {
    Null,
    Bool(bool),
    Int(i64),
    String(*const String),
}
```

This way the size of our `Object` will be 16 bytes. 8 bytes for the `i64` or raw pointer and another 8 bytes for the enum discriminant. But what if we store the discriminant _inside_ the pointer or value?

Because of said memory alignment, [memory addresses on 64-bit architectures](https://en.wikipedia.org/wiki/Tagged_pointer#Folding_tags_into_the_pointer) will also be byte aligned. This leaves the 3 least significant bits unused, as these will always be 0. 

There are currently only 6 different types of values in Nederlang, so 3 bits will be sufficient to store our type information in. 

The new `Object` type will be a thin wrapper around a raw pointer:

```rust
struct Object(*mut u8);
```

Let's use a separate enum for a `Type` tag:

```rust
#[repr(u8)]
enum Type {
    // The types below are all stored directly inside the pointer
    Null = 0b000,
    Int,
    Bool,
    Function,

    // The types below are all heap-allocated
    Float,
    String,
}
```

We'll introduce some helper methods for creating a new object with a given type and retrieving just the type tag.

```rust
impl<'a> Object {
    /// Creates a new object from the value (or address) with the given type mask applied
    fn with_type(raw: *mut u8, t: Type) -> Self {
        Self((raw as usize | t as usize) as _)
    }

    fn get_type(self) -> Type {
        // Safety: self.0 with TAG_MASK applied will always yield a correct Type
        unsafe { std::mem::transmute((self.0 as usize & 0b111) as u8) }
    }
}
```

Here's a method to create an Object holding an integer value with the `Type::Int` tag.

```rust
impl<'a> Object {
    /// Create a new integer value
    fn int(value: i64) -> Self {
        // assert there is no data loss because of the shift
        debug_assert_eq!(((value << 3) >> 3), value);
        Self::with_type((value << 3) as _, Type::Int)
    }
}
```

As you can see we're losing 3 bits because of the type tag, so our maximum integer value will now be 2^60 instead of 2^63. 

To later retrieve the integer value, we simply shift 3 bits to the right right again (discarding the type tag).

```rust
impl<'a> Object {
    /// Returns the integer value of this object pointer
    /// The caller should ensure the object is of the correct type
    fn as_int(self) -> i64 {
        self.0 as i64 >> 3
    }
}
```

Storing and retrieving a raw pointer is very similar, except to retrieve the address we reset the lowest 3 bits to `0` instead of shifting.

```rust
impl<'a> Object {
    fn as_ptr(self) -> *mut u8 {
        (self.0 as usize & !0b111) as _
    }

    unsafe fn get<T>(self) -> &'a T {
        &*(self.as_ptr() as *const T)
    }
}
```

So now if we have an `Object` we can do the following to get a reference to the `String` value it points to:

```rust
assert_eq!(obj.get_type(), Type::String);
let str = obj.get::<String>();
```

You can [view the complete tagged pointer implementation in this commit](https://github.com/dannyvankooten/nederlang/commit/6bacf8a7107beed13a46262ba6aeb02c003dca05).

OK. Now our `Object` type has shrunk to just 8 bytes, meaning it fits into a register! 

What does that yield us in terms of performance? Let's run `fb` again to find out.

<div style="overflow-x: scroll;">

| Command | Mean [s] | Min [s] | Max [s] |
|:---|---:|---:|---:|
| `nederlang fib.nl` | 2.093 ± 0.027 | 2.049 | 2.118 |

</div>

BOOM! **2.1 seconds**, down from 3.7. A 43% performance improvement. Worth it if you ask me.


#### (Manually) inlining the hot path

We've squeezed most performance out of the tree walker by now, but there are still some things we can do. We can compile an optimized binary using [profile guided optimization](https://doc.rust-lang.org/rustc/profile-guided-optimization.html). While that shaved off another few percent for me, it feels a bit too much like cheating. 

One other thing is to manually instruct the compiler what functions to inline, trading binary size for runtime performance. Let's take a look at the binary size before spraying `[inline]` all over our code.

```
ls -lh target/release/nederlang
-rwxr-xr-x 2 danny danny 2.1M Nov 22 09:57 target/release/nederlang*
```

And after [inlining all of the hot path](https://github.com/dannyvankooten/nederlang/commit/5f88a7ac769c317873dd5bfb88732ba5703dfab6):

```
ls -lh target/release/nederlang
-rwxr-xr-x 2 danny danny 2.1M Nov 22 09:59 target/release/nederlang*
```

No change in size! Yet we know it worked since running `perf` now shows us that all of our hot functions were inlined:

```
99.99%    84.80%  [.] nederlang::eval::eval_expr (inlined)
99.99%     0.00%  [.] nederlang::eval::eval_if_expr (inlined)
99.99%     0.00%  [.] nederlang::eval::eval_infix_expr (inlined)
99.99%     0.00%  [.] nederlang::eval::eval_block (inlined)
99.99%     0.00%  [.] nederlang::eval::eval_call_expr (inlined)
43.08%     0.00%  [.] nederlang::eval::Environment::resolve (inlined)
```

So... Did it yield a significant performance improvement? Let's ask hyperfine.

<div style="overflow-x: scroll;">

| Command | Mean [s] | Min [s] | Max [s] |
|:---|---:|---:|---:|
| `nederlang fib.nl` | 1.873 ± 0.017 | 1.854 | 1.895 |

</div>

**1.8** seconds, an 11% improvement. Free speed!

#### Further optimizations: bytecode compilation

That's about as far as I want to go with this tree walker.

The most obvious remaining performance improvement will come from not evaluating the AST directly but transforming it into (CPU-cache efficient) bytecode, applying all kinds of optimizations at compile time and then executing the instructions inside a virtual machine. 

## My experience writing Rust versus C

In the past few weeks I've grown considerably more comfortable writing Rust code, grasping lifetimes and dealing with Rust's tooling ecosystem. 

I've now implemented and optimized a simple interpreted programming language in both C and Rust. I think it is easy to write performant C code, but it's very hard to write safe/leak-free C code. Especially for a newcomer. 

Rust manages to reverse that default. It is very easy to write safe/leak-free Rust code, yet a newcomer to the language might have to spend some time optimizing that code for performance. 

This really only applies to newcomers (like me) though. In hindsight most of the performance optimizations in this post were pretty obvious and trivial to fix, now I know what to watch for.

---

<small>I've prepared a [GitHub branch containing the various optimizations described here](https://github.com/dannyvankooten/nederlang/commits/tree-walker), so you can look at all of the actual code. Note that the order of optimizations might differ slightly from the order in this post.</small>


