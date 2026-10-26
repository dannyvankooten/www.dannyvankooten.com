#!/usr/bin/env node
// Imports posts, pages and images from the live WordPress site.
//
// Usage: node scripts/import-wordpress.mjs [--site=https://www.dannyvankooten.com]
//
// Bunny CDN caches the REST API by path and ignores the query string, so list
// endpoints (?per_page=100&page=2) can't be used. Instead, items are discovered
// through the WordPress sitemaps and fetched one by one by ID. Post IDs come from
// the `postid-N` / `page-id-N` body class of each page.
//
// Bunny Shield blocks a few REST responses (e.g. posts containing SQL). For those,
// the content is taken from the rendered HTML page instead.
//
// Images are saved to src/assets/images/{blog/YEAR/SLUG,pages/SLUG}/. Entries with
// images are written as MDX, so the images are rendered through Astro's <Picture>.

import { mkdir, writeFile, rm } from 'node:fs/promises';
import { basename, dirname, join, relative, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import { parseHTML } from 'linkedom';
import TurndownService from 'turndown';
import { gfm } from 'turndown-plugin-gfm';
import redirects from '../redirects.mjs';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..');
const SITE = (process.argv.find((a) => a.startsWith('--site='))?.slice(7) ?? 'https://www.dannyvankooten.com').replace(/\/$/, '');
const SITE_HOST_RE = /^https?:\/\/(?:www\.)?dannyvankooten\.com/;
const UPLOADS_RE = /^(?:https?:\/\/(?:www\.)?dannyvankooten\.com)?(\/wp-content\/uploads\/[^\s"'?#)]+)/;
const IMAGE_EXT_RE = /\.(png|jpe?g|gif|webp|svg|avif)$/i;
const DEFAULT_OG_IMAGE = '/wp-content/themes/dvk26/assets/images/og-default.png';

// The front page is a regular WordPress page; it becomes src/content/pages/index.md
const FRONT_PAGE_SLUG = 'danny-van-kooten';

// Code block languages that Shiki doesn't know
const CODE_LANGUAGES = { filename: 'text' };

// Non-image uploads that are linked from content. These are kept in public/.
const files = new Set();

async function get(url, type = 'text') {
	const res = await fetch(url, { headers: { 'User-Agent': 'dvk-astro-import' } });
	if (!res.ok) {
		throw new Error(`${res.status} ${res.statusText} for ${url}`);
	}
	return type === 'json' ? res.json() : res.text();
}

async function sitemapUrls(type) {
	const xml = await get(`${SITE}/wp-sitemap-posts-${type}-1.xml`);
	return [...xml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => m[1]);
}

/** Maps an absolute URL on this site to a root-relative one, following known redirects. */
function localPath(url) {
	if (!SITE_HOST_RE.test(url)) {
		return url;
	}
	let path = url.replace(SITE_HOST_RE, '') || '/';
	const [pathname, rest = ''] = path.split(/(?=[?#])/);
	return (redirects[pathname] ?? pathname) + rest;
}

function decodeEntities(str) {
	const { document } = parseHTML('<!doctype html><html><body></body></html>');
	// linkedom doesn't decode entities in a <textarea>, so use the text of a <div>
	const el = document.createElement('div');
	el.innerHTML = str;
	return el.textContent;
}

/** Reads the bits of a rendered page that the REST API doesn't give us. */
function scrapePage(html) {
	const { document } = parseHTML(html);
	const meta = (sel) => document.querySelector(sel)?.getAttribute('content') ?? '';
	const bodyClass = document.body.className;
	const article = document.querySelector('article');
	const ogImage = meta('meta[property="og:image"]');

	return {
		id: Number(bodyClass.match(/\b(?:postid|page-id)-(\d+)\b/)?.[1]),
		description: meta('meta[name="description"]'),
		image: ogImage && !ogImage.endsWith(DEFAULT_OG_IMAGE) ? ogImage : '',
		categories: [...(article?.className ?? '').matchAll(/\bcategory-([a-z0-9-]+)/g)].map((m) => m[1]),
		// Only used when the REST API response is blocked
		fallback: {
			title: document.querySelector('h1')?.innerHTML ?? '',
			date: meta('meta[property="article:published_time"]'),
			modified: meta('meta[property="article:modified_time"]'),
			content: document.querySelector('.e-content')?.innerHTML ?? '',
		},
	};
}

/** WordPress REST dates are local time without an offset, so derive it from the GMT variant. */
function isoDate(local, gmt) {
	const offset = Math.round((Date.parse(`${local}Z`) - Date.parse(`${gmt}Z`)) / 60000);
	const sign = offset < 0 ? '-' : '+';
	const hh = String(Math.floor(Math.abs(offset) / 60)).padStart(2, '0');
	const mm = String(Math.abs(offset) % 60).padStart(2, '0');
	return `${local}${sign}${hh}:${mm}`;
}

/** A JSX attribute, quoted as an expression when the value would need escaping. */
const jsxAttr = (name, value) => (/["{}<>\\]/.test(value) ? `${name}={${JSON.stringify(value)}}` : `${name}="${value}"`);

function createTurndown({ mdx }) {
	const td = new TurndownService({
		headingStyle: 'atx',
		hr: '---',
		bulletListMarker: '-',
		codeBlockStyle: 'fenced',
		emDelimiter: '_',
		br: '\\',
	});
	td.use(gfm);

	if (mdx) {
		// MDX treats { and < as the start of expressions and JSX
		const escape = td.escape.bind(td);
		td.escape = (str) => escape(str).replace(/[{}<]/g, '\\$&');
	}

	// Turndown's fenced code rule only looks at the first class; WordPress puts the
	// language class on <code>, which is what we want.
	td.addRule('fencedCode', {
		filter: (node) => node.nodeName === 'PRE' && node.firstChild?.nodeName === 'CODE',
		replacement: (_content, node) => {
			const code = node.firstChild;
			const lang = code.className.match(/language-(\S+)/)?.[1] ?? '';
			const text = code.textContent.replace(/\n$/, '');
			const fence = text.includes('```') ? '~~~~' : '```';
			return `\n\n${fence}${CODE_LANGUAGES[lang] ?? lang}\n${text}\n${fence}\n\n`;
		},
	});

	// Figures with a caption and galleries: Markdown images inside a JSX <figure>
	td.addRule('figure', {
		filter: (node) => node.nodeName === 'FIGURE' && (node.querySelector('figcaption') || node.classList.contains('gallery')),
		replacement: (_content, node) => {
			// Turndown's DOM has no iterable NodeList
			const images = Array.from(node.querySelectorAll('img')).map((img) => td.turndown(img.outerHTML)).join('\n');
			const caption = node.querySelector('figcaption');
			const cls = node.classList.contains('gallery') ? ' class="gallery"' : '';
			const figcaption = caption ? `<figcaption>${td.turndown(caption.innerHTML)}</figcaption>\n` : '';
			return `\n\n<figure${cls}>\n\n${images}\n\n${figcaption}</figure>\n\n`;
		},
	});

	// A styled link around an image, e.g. a logo that needs a light background
	td.addRule('styledImageLink', {
		filter: (node) => node.nodeName === 'A' && node.hasAttribute('style') && node.children.length === 1 && node.firstElementChild.nodeName === 'IMG',
		replacement: (_content, node) =>
			`\n\n<a ${jsxAttr('href', node.getAttribute('href'))} ${jsxAttr('style', node.getAttribute('style'))}>${td.turndown(node.innerHTML)}</a>\n\n`,
	});

	return td;
}

const RAW_TAGS = new Set(['STYLE', 'SCRIPT', 'FORM', 'MATH', 'SMALL', 'SUP', 'SUB', 'VAR', 'CENTER', 'DIV']);

/** Elements Markdown can't express. These are kept as raw HTML. */
function isRaw(el) {
	if (el.querySelector('img')) {
		// Images are always converted, so they can be optimized
		return false;
	}
	return (
		RAW_TAGS.has(el.nodeName) ||
		(el.hasAttribute('style') && el.nodeName !== 'TD' && el.nodeName !== 'TH') ||
		// Markdown would still process the text around inline HTML, e.g. autolink URLs inside an <a>
		(el.nodeName === 'P' && [...el.querySelectorAll('*')].some(isRaw))
	);
}

/**
 * Swaps raw HTML elements for placeholders, so Turndown doesn't touch them
 * (it would collapse whitespace inside <script>, for example).
 */
function extractRawHtml(body) {
	const raw = [];
	for (const el of body.querySelectorAll('*')) {
		if (!el.isConnected || !isRaw(el)) continue;
		const token = `RAWHTML${raw.length}END`;
		raw.push({ token, html: el.outerHTML });
		const placeholder = el.ownerDocument.createElement(el.parentElement === body ? 'p' : 'span');
		placeholder.textContent = token;
		el.replaceWith(placeholder);
	}
	return raw;
}

/**
 * In Markdown, CommonMark ends most HTML blocks at the first blank line, so raw HTML
 * must not contain any. <script> and <style> blocks run until their closing tag.
 *
 * MDX doesn't allow raw HTML at all, so it is passed as a string to <Fragment set:html>.
 */
function restoreRawHtml(md, raw, { mdx }) {
	for (const { token, html } of raw) {
		let safe = /^<(script|style)\b/.test(html) ? html : html.replace(/\n\s*\n/g, '\n');
		if (mdx) {
			safe = `<Fragment set:html={\`${safe.replace(/[\\`]/g, '\\$&').replace(/\$\{/g, '\\${')}\`} />`;
		}
		md = md.replace(token, () => safe);
	}
	return md;
}

/** Collects the images of a single post or page and decides where they are stored. */
class ImageCollector {
	constructor(contentFile, assetDir) {
		this.contentFile = contentFile;
		this.assetDir = assetDir;
		/** @type {Map<string, string[]>} asset file => candidate upload paths, best first */
		this.images = new Map();
	}

	/** Registers an upload and returns its path relative to the content file. */
	add(uploadPath) {
		uploadPath = decodeURIComponent(uploadPath);
		// WordPress serves resized copies (image-540x360.png); Astro resizes the original
		const original = uploadPath.replace(/-\d+x\d+(\.\w+)$/, '$1');
		let name = basename(original).replace(/[^\w.-]+/g, '-');
		let file = join(this.assetDir, name);
		const existing = this.images.get(file);
		if (existing && existing[0] !== original) {
			name = `${basename(dirname(original))}-${name}`;
			file = join(this.assetDir, name);
		}
		this.images.set(file, [original, uploadPath]);
		return this.relative(file);
	}

	relative(file) {
		return relative(dirname(this.contentFile), file).split(sep).join('/');
	}
}

/** Normalizes WordPress block markup before it is converted to Markdown. */
function cleanHtml(html, images) {
	const { document } = parseHTML(`<!doctype html><html><body>${html}</body></html>`);
	const body = document.body;

	// WP embeds: keep the fallback blockquote link, drop the iframe that needs wp-embed.js
	body.querySelectorAll('iframe.wp-embedded-content').forEach((el) => el.remove());
	body.querySelectorAll('blockquote.wp-embedded-content').forEach((el) => {
		el.removeAttribute('class');
		el.removeAttribute('data-secret');
		const p = el.parentElement;
		// WordPress wraps the embed in a <p>, which isn't valid around a <blockquote>
		if (p?.nodeName === 'P' && p.children.length === 1) {
			p.replaceWith(el);
		}
	});

	// Group blocks are layout-only wrappers
	for (const el of [...body.querySelectorAll('.wp-block-group, .wp-block-group__inner-container')].reverse()) {
		el.replaceWith(...el.childNodes);
	}

	// Table cell alignment becomes a GFM alignment row
	body.querySelectorAll('td[style], th[style]').forEach((el) => {
		const align = el.getAttribute('style').match(/text-align:\s*(left|right|center)/)?.[1];
		if (align) {
			el.setAttribute('align', align);
		}
		el.removeAttribute('style');
	});

	// Links from an image to the full-size upload or its attachment page are dropped;
	// the optimized image is responsive already.
	body.querySelectorAll('a > img:only-child').forEach((img) => {
		const href = localPath(img.parentElement.getAttribute('href') ?? '');
		if (UPLOADS_RE.test(href) || /^\/blog\/\d{4}\/[^/]+\/[^/]+\/$/.test(href)) {
			img.parentElement.replaceWith(img);
		}
	});

	body.querySelectorAll('img').forEach((img) => {
		const upload = img.getAttribute('src')?.match(UPLOADS_RE);
		if (upload) {
			img.setAttribute('src', images.add(upload[1]));
		} else {
			console.warn(`  ! Image is not an upload, it won't be optimized: ${img.getAttribute('src')}`);
		}
		for (const attr of ['width', 'height', 'srcset', 'sizes', 'title']) {
			img.removeAttribute(attr);
		}
	});

	body.querySelectorAll('[href], [src]').forEach((el) => {
		const attr = el.hasAttribute('href') ? 'href' : 'src';
		const value = el.getAttribute(attr);
		const upload = value.match(UPLOADS_RE);
		if (upload && el.nodeName !== 'IMG') {
			files.add(upload[1]);
		}
		el.setAttribute(attr, localPath(value));
		el.removeAttribute('data-type');
		el.removeAttribute('data-id');
	});

	// Strip WordPress-generated attributes from what remains as raw HTML
	body.querySelectorAll('*').forEach((el) => {
		el.removeAttribute('decoding');
		el.removeAttribute('loading');
		el.removeAttribute('fetchpriority');
		const cls = (el.getAttribute('class') ?? '')
			.split(/\s+/)
			.filter((c) => c && !/^(wp-|has-|is-|size-|columns-|alignwide|alignfull|aligncenter$|wp-image-)/.test(c))
			.join(' ');
		if (cls) {
			el.setAttribute('class', cls);
		} else {
			el.removeAttribute('class');
		}
	});
	body.querySelectorAll('figure').forEach((el) => {
		if (el.querySelector('figure')) {
			el.setAttribute('class', 'gallery');
		}
	});

	return body;
}

function toMarkdown(body, { mdx }) {
	const raw = extractRawHtml(body);
	const md = createTurndown({ mdx }).turndown(body.innerHTML).replace(/\n{3,}/g, '\n\n');
	return restoreRawHtml(md, raw, { mdx }).trim() + '\n';
}

/**
 * WordPress falls back to the first 30 words of the content for the meta
 * description. Those are regenerated at build time, so only hand-written
 * descriptions are imported.
 */
function customDescription(description, content) {
	const normalize = (str) => decodeEntities(str).toLowerCase().replace(/[^a-z0-9]/g, '');
	const text = normalize(parseHTML(`<!doctype html><html><body>${content}</body></html>`).document.body.textContent);
	const desc = normalize(description);
	return desc && !text.startsWith(desc) ? decodeEntities(description) : undefined;
}

const yamlString = (str) => JSON.stringify(str);

function frontmatter(fields) {
	const lines = ['---'];
	for (const [key, value] of Object.entries(fields)) {
		if (value === undefined || value === '' || (Array.isArray(value) && value.length === 0)) {
			continue;
		}
		if (Array.isArray(value)) {
			lines.push(`${key}: [${value.map(yamlString).join(', ')}]`);
		} else {
			lines.push(`${key}: ${yamlString(value)}`);
		}
	}
	lines.push('---', '');
	return lines.join('\n');
}

const categoryCache = new Map();
async function categoryName(id) {
	if (!categoryCache.has(id)) {
		categoryCache.set(id, get(`${SITE}/wp-json/wp/v2/categories/${id}`, 'json').then((c) => decodeEntities(c.name)));
	}
	return categoryCache.get(id);
}

async function importItem(type, url) {
	const html = await get(url);
	const page = scrapePage(html);
	if (!page.id) {
		throw new Error(`Could not find the ${type} ID for ${url}`);
	}

	let item;
	try {
		const wp = await get(`${SITE}/wp-json/wp/v2/${type}s/${page.id}`, 'json');
		item = {
			title: decodeEntities(wp.title.rendered),
			date: isoDate(wp.date, wp.date_gmt),
			modified: isoDate(wp.modified, wp.modified_gmt),
			content: wp.content.rendered,
			categories: await Promise.all(wp.categories?.map(categoryName) ?? []),
			slug: wp.slug,
			link: wp.link,
		};
	} catch (err) {
		console.warn(`  ! REST API failed for ${url} (${err.message}), using rendered HTML`);
		item = {
			...page.fallback,
			title: decodeEntities(page.fallback.title),
			categories: page.categories,
			slug: url.split('/').filter(Boolean).pop(),
			link: url,
		};
	}

	const path = new URL(item.link).pathname.replace(/^\/|\/$/g, '');
	const name = type === 'post' ? path.replace(/^blog\//, '') : item.slug === FRONT_PAGE_SLUG ? 'index' : path;
	const collection = type === 'post' ? 'posts' : 'pages';
	const assetDir = join(ROOT, 'src/assets/images', type === 'post' ? path : `pages/${name}`);

	// The extension depends on whether there are images, so the path is resolved
	// with a placeholder extension first. Both live in the same directory.
	const images = new ImageCollector(join(ROOT, 'src/content', collection, `${name}.md`), assetDir);
	const body = cleanHtml(item.content, images);
	const mdx = images.images.size > 0;
	const file = join(ROOT, 'src/content', collection, `${name}.${mdx ? 'mdx' : 'md'}`);

	const ogImage = page.image.match(UPLOADS_RE);
	const fields = {
		title: item.title,
		description: customDescription(page.description, item.content),
		date: type === 'post' ? item.date : undefined,
		modified: type === 'post' ? item.modified : undefined,
		image: ogImage ? images.add(ogImage[1]) : undefined,
		categories: type === 'post' ? item.categories : undefined,
	};

	await mkdir(dirname(file), { recursive: true });
	await writeFile(file, frontmatter(fields) + '\n' + toMarkdown(body, { mdx }));
	await downloadImages(images);
	return file;
}

async function download(uploadPath, file) {
	const res = await fetch(SITE + encodeURI(uploadPath));
	if (!res.ok) {
		return false;
	}
	await mkdir(dirname(file), { recursive: true });
	await writeFile(file, Buffer.from(await res.arrayBuffer()));
	return true;
}

async function downloadImages({ images }) {
	for (const [file, candidates] of images) {
		let ok = false;
		for (const uploadPath of new Set(candidates)) {
			if ((ok = await download(uploadPath, file))) break;
		}
		if (!ok) {
			console.warn(`  ! Could not download ${candidates.join(' or ')}`);
		}
	}
}

async function main() {
	for (const dir of ['src/content/posts', 'src/content/pages', 'src/assets/images', 'public/wp-content']) {
		await rm(join(ROOT, dir), { recursive: true, force: true });
	}

	for (const type of ['post', 'page']) {
		const urls = await sitemapUrls(type);
		console.log(`Importing ${urls.length} ${type}s`);
		for (const url of urls) {
			const file = await importItem(type, url);
			console.log(`  ${file.replace(ROOT + '/', '')}`);
		}
	}

	for (const path of files) {
		if (IMAGE_EXT_RE.test(path)) {
			console.warn(`  ! Linked image is not displayed anywhere, skipping: ${path}`);
		} else if (!(await download(path, join(ROOT, 'public', decodeURIComponent(path))))) {
			console.warn(`  ! Could not download ${path}`);
		}
	}
}

main().catch((err) => {
	console.error(err);
	process.exit(1);
});
