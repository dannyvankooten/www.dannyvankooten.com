import type { APIContext } from 'astro';
import { getCollection } from 'astro:content';
import { SITE_DESCRIPTION, SITE_TITLE } from '../consts';
import { description, getPosts, pageUrl, postUrl } from '../lib/content';

export async function GET(context: APIContext) {
	const site = context.site!;
	const link = (title: string, path: string, desc?: string) => `- [${title}](${new URL(path, site)})${desc ? `: ${desc}` : ''}`;
	const pages = await getCollection('pages');
	const home = pages.find((page) => page.id === 'index');

	const lines = [
		`# ${SITE_TITLE}`,
		'',
		`> ${home ? description(home) : SITE_DESCRIPTION}`,
		'',
		'## Pages',
		...pages.map((page) => link(page.data.title, pageUrl(page))),
		'',
		'## Posts',
		...(await getPosts()).map((post) => link(post.data.title, postUrl(post), description(post))),
		'',
	];

	return new Response(lines.join('\n'), {
		headers: { 'Content-Type': 'text/plain; charset=utf-8' },
	});
}
