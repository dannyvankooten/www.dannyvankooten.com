import { type CollectionEntry, getCollection } from 'astro:content';
import { SITE_LOCALE, SITE_TIMEZONE } from '../consts';

export type Post = CollectionEntry<'posts'>;
export type Page = CollectionEntry<'pages'>;

/** All posts, newest first. */
export async function getPosts(): Promise<Post[]> {
	const posts = await getCollection('posts');
	return posts.sort((a, b) => b.data.date.valueOf() - a.data.date.valueOf());
}

export function postUrl(post: Post): string {
	return `/blog/${post.id}/`;
}

export function pageUrl(page: Page): string {
	return page.id === 'index' ? '/' : `/${page.id}/`;
}

/** "October 6, 2026", in the site's timezone. */
export function formatDate(date: Date): string {
	return date.toLocaleDateString(SITE_LOCALE, { timeZone: SITE_TIMEZONE, year: 'numeric', month: 'long', day: 'numeric' });
}

/** "2026-10-06", in the site's timezone. */
export function isoDate(date: Date): string {
	return date.toLocaleDateString('en-CA', { timeZone: SITE_TIMEZONE });
}

export function postYear(post: Post): string {
	return isoDate(post.data.date).slice(0, 4);
}

/** Posts grouped by year, newest year first. */
export function groupByYear(posts: Post[]): [string, Post[]][] {
	const groups = new Map<string, Post[]>();
	for (const post of posts) {
		const year = postYear(post);
		groups.set(year, [...(groups.get(year) ?? []), post]);
	}
	return [...groups];
}

/** Plain text from an HTML string, block elements separated by spaces. */
export function stripTags(html: string): string {
	return html
		.replace(/<(script|style)\b[\s\S]*?<\/\1>/gi, ' ')
		.replace(/<[^>]+>/g, ' ')
		.replace(/&nbsp;/g, ' ')
		.replace(/&amp;/g, '&')
		.replace(/&lt;/g, '<')
		.replace(/&gt;/g, '>')
		.replace(/&quot;/g, '"')
		.replace(/&#0?39;/g, "'")
		.replace(/\s+/g, ' ')
		.replace(/ ([.,;:!?)])/g, '$1')
		.trim();
}

/** Plain text from Markdown or MDX source, good enough for descriptions. */
function stripMarkdown(source: string): string {
	return stripTags(
		source
			.replace(/^(?:import|export) .*$/gm, '')
			.replace(/^(`{3,}|~{3,})[\s\S]*?^\1/gm, '')
			.replace(/<Fragment set:html=\{`[\s\S]*?`\} \/>/g, '')
			.replace(/!\[[^\]]*\]\([^)]*\)/g, '')
			.replace(/\[([^\]]*)\]\([^)]*\)/g, '$1')
			.replace(/^#+ |[*_`]|\\(?=\S)/gm, ''),
	);
}

/** The entry's description, or its first 30 words, like the WordPress SEO plugin did. */
export function description(entry: Post | Page): string {
	if (entry.data.description) {
		return entry.data.description;
	}
	// MDX entries aren't rendered ahead of time, so they only have their source
	const text = entry.rendered ? stripTags(entry.rendered.html) : stripMarkdown(entry.body ?? '');
	return text.split(' ').slice(0, 30).join(' ');
}
