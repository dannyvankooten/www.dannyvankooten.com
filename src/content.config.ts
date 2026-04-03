import { defineCollection } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

const blog = defineCollection({
  loader: glob({ 
    pattern: "*.md", 
    base: "./src/content/blog",
    generateId: ({ entry }) => entry.replace(/-\d{2}-\d{2}-/, '/').replace(/\.md$/, ''),
 }),
    schema: z.object({
        title: z.string(),
        description: z.string().optional(),
        datePublished: z.coerce.date()
    }),
});
const pages = defineCollection({
    loader: glob({ 
        pattern: "*.md", 
        base: "./src/content"
    }),
    schema: z.object({
        title: z.string()
    }),
});

export const collections = { blog, pages };