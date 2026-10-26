import { defineCollection } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

// Posts live at src/content/posts/{year}/{slug}.md(x) and are served at /blog/{year}/{slug}/.
// Use MDX for posts with images, so that they are optimized (see ContentImage.astro).
const posts = defineCollection({
	loader: glob({ base: './src/content/posts', pattern: '**/*.{md,mdx}' }),
	schema: ({ image }) =>
		z.object({
			title: z.string(),
			// Falls back to the first words of the content
			description: z.string().optional(),
			date: z.coerce.date(),
			modified: z.coerce.date().optional(),
			// Open Graph image, relative to the entry
			image: image().optional(),
			categories: z.array(z.string()).default([]),
		}),
});

// Pages live at src/content/pages/{slug}.md(x) and are served at /{slug}/. index.md is the front page.
const pages = defineCollection({
	loader: glob({ base: './src/content/pages', pattern: '**/*.{md,mdx}' }),
	schema: ({ image }) =>
		z.object({
			title: z.string(),
			description: z.string().optional(),
			image: image().optional(),
		}),
});

export const collections = { posts, pages };
