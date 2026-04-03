import { getCollection } from 'astro:content';

export async function GET() {
  const posts = (await getCollection('blog')).sort(
    (a, b) => b.data.datePublished.valueOf() - a.data.datePublished.valueOf(),
  );

  const sections = posts.map((p) => {
    const date = p.data.datePublished.toISOString().slice(0, 10);
    return `# ${p.data.title}\n\nDate: ${date}\nURL: https://www.dannyvankooten.com/blog/${p.id}/\n\n${p.body}`;
  });

  const content = [
    `# Danny van Kooten — Full Blog Content`,
    ``,
    `> This file contains all blog posts in markdown format.`,
    ``,
    `---`,
    ``,
    sections.join('\n\n---\n\n'),
  ].join('\n');

  return new Response(content, {
    headers: { 'Content-Type': 'text/plain; charset=utf-8' },
  });
}
