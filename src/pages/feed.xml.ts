import rss from '@astrojs/rss';
import type { APIContext } from 'astro';
import { SITE_DESCRIPTION, SITE_TITLE } from '../consts';
import { description, getPosts, postUrl } from '../lib/content';
import { renderToHtml } from '../lib/render';

/** Feed readers need plain <img> elements with absolute URLs. */
function feedHtml(html: string, site: URL): string {
	return html
		.replace(/<\/?picture>|<source [^>]*>/g, '')
		.replace(/\s(?:srcset|sizes)="[^"]*"/g, '')
		.replace(/(href|src)="\/(?!\/)/g, `$1="${site.origin}/`);
}

export async function GET(context: APIContext) {
	const site = context.site!;
	const posts = (await getPosts()).slice(0, 10);

	return rss({
		title: SITE_TITLE,
		description: SITE_DESCRIPTION,
		site,
		items: await Promise.all(
			posts.map(async (post) => ({
				title: post.data.title,
				link: postUrl(post),
				pubDate: post.data.date,
				description: description(post),
				content: feedHtml(await renderToHtml(post), site),
				categories: post.data.categories,
			})),
		),
		customData: '<language>en-US</language>',
	});
}
