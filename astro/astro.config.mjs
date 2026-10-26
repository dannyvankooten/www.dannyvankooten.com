// @ts-check

import { globSync, readFileSync } from 'node:fs';
import mdx from '@astrojs/mdx';
import sitemap from '@astrojs/sitemap';
import seoGraph from '@jdevalk/astro-seo-graph/integration';
import { defineConfig, fontProviders } from 'astro/config';
import redirects from './redirects.mjs';

const site = 'https://www.dannyvankooten.com';

/** Sitemap lastmod per post URL, from the `modified` (or `date`) frontmatter field. */
function postLastmod() {
	const lastmod = new Map();
	for (const file of globSync('src/content/posts/**/*.{md,mdx}')) {
		const frontmatter = readFileSync(file, 'utf8').split(/^---$/m)[1] ?? '';
		const field = (/** @type {string} */ name) => frontmatter.match(new RegExp(`^${name}:\\s*"?([^"\\n]+)"?`, 'm'))?.[1];
		const date = field('modified') ?? field('date');
		const id = file.replace(/^src\/content\/posts\/|\.mdx?$/g, '');
		if (date) lastmod.set(`${site}/blog/${id}/`, new Date(date));
	}
	// The homepage lists all posts, so it changes whenever one does
	lastmod.set(`${site}/`, new Date(Math.max(...lastmod.values())));
	return lastmod;
}
const lastmod = postLastmod();

// https://astro.build/config
export default defineConfig({
	site,
	redirects,
	build: {
		// Redirects are served by NGINX (dist/nginx-redirects.conf), so no meta refresh pages
		redirects: false,
	},
	integrations: [
		mdx(),
		sitemap({
			serialize(item) {
				const date = lastmod.get(item.url);
				return date ? { ...item, lastmod: date.toISOString() } : item;
			},
		}),
		// Build-time SEO checks; problems are reported as warnings
		seoGraph({
			validateH1: true,
			validateUniqueMetadata: true,
			validateImageAlt: true,
			validateMetadataLength: true,
			validateInternalLinks: {
				// Files kept from WordPress in public/; .html files there are mistaken for pages
				skip: (href) => href.startsWith('/wp-content/uploads/'),
			},
		}),
	],
	markdown: {
		shikiConfig: {
			theme: 'vitesse-dark',
		},
	},
	fonts: [
		{
			provider: fontProviders.local(),
			name: 'Inter',
			cssVariable: '--font-inter',
			fallbacks: ['Helvetica Neue', 'Nimbus Sans', 'Arial', 'sans-serif'],
			options: {
				variants: [
					{
						src: ['./src/assets/fonts/InterVariable.woff2'],
						weight: '100 900',
						style: 'normal',
						display: 'swap',
					},
					{
						src: ['./src/assets/fonts/InterVariable-Italic.woff2'],
						weight: '100 900',
						style: 'italic',
						display: 'swap',
					},
				],
			},
		},
	],
});
