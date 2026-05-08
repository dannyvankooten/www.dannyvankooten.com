import { getCollection } from 'astro:content';

export async function GET() {
  const posts = (await getCollection('blog')).sort(
    (a, b) => b.data.datePublished.valueOf() - a.data.datePublished.valueOf(),
  );

  const lines = [
    `# Danny van Kooten`,
    ``,
    `> Personal website of Danny van Kooten, founder of ibericode. WordPress plugin developer behind Mailchimp for WordPress (2M+ active installs) and Koko Analytics.`,
    ``,
    `## Pages`,
    ``,
    `- [About](https://www.dannyvankooten.com/about/): About Danny van Kooten`,
    `- [Projects](https://www.dannyvankooten.com/projects/): Selection of projects and open-source work`,
    `- [WordPress Plugins](https://www.dannyvankooten.com/wordpress-plugins/): WordPress plugins by Danny`,
    ``,
    `## Blog Posts`,
    ``,
    ...posts.map(
      (p) => `- [${p.data.title}](https://www.dannyvankooten.com/blog/${p.id}/): ${p.data.description || ''}`.trimEnd(),
    ),
    ``,
    `## Optional`,
    ``,
    `- [llms-full.txt](https://www.dannyvankooten.com/llms-full.txt): Full content of all blog posts in markdown`,
  ];

  return new Response(lines.join('\n'), {
    headers: { 'Content-Type': 'text/plain; charset=utf-8' },
  });
}
