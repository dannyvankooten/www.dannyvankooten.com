import { defineCollection } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

const blog = defineCollection({
  loader: glob({ 
    pattern: "*.md", 
    base: "./src/content/blog",
    generateId: ({ entry }) => entry.replace(/-\d{2}-\d{2}-/, '/').replace(/\.md$/, ''),
 }),
    schema: ({ image }) => z.object({
        title: z.string().min(5).max(120),
        description: z.string().min(15).max(160),
        datePublished: z.coerce.date(),
        image: image().optional(),
    }),
});
const pages = defineCollection({
    loader: glob({ 
        pattern: "*.md", 
        base: "./src/content"
    }),
    schema: z.object({
        title: z.string().min(5).max(120),
        description: z.string().min(15).max(160),
    }),
});

export const collections = { blog, pages };