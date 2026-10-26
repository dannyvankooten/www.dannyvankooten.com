import { getContainerRenderer } from '@astrojs/mdx/container-renderer';
import { experimental_AstroContainer as AstroContainer } from 'astro/container';
import { loadRenderers } from 'astro:container';
import { render } from 'astro:content';
import ContentImage from '../components/ContentImage.astro';
import type { Page, Post } from './content';

let container: AstroContainer | undefined;

/** Renders the content of a Markdown or MDX entry to an HTML string, e.g. for feeds. */
export async function renderToHtml(entry: Post | Page): Promise<string> {
	container ??= await AstroContainer.create({
		renderers: await loadRenderers([getContainerRenderer()]),
	});
	const { Content } = await render(entry);
	return container.renderToString(Content, { props: { components: { img: ContentImage } } });
}
