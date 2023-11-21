package main

import (
	"bytes"
	"encoding/xml"
	"errors"
	"github.com/yuin/goldmark"
	"github.com/yuin/goldmark/extension"
	"github.com/yuin/goldmark/renderer/html"
	"html/template"
	"io"
	"io/fs"
	"log"
	"os"
	"path/filepath"
	"strings"
	"time"
)

var md = goldmark.New(
	goldmark.WithExtensions(extension.GFM),
	goldmark.WithParserOptions(),
	goldmark.WithRendererOptions(
		html.WithUnsafe(),
	),
)

var templates = template.Must(template.ParseFS(os.DirFS("templates/"), "*.html", "*.xml"))

type Page struct {
	Title      string
	Template   string
	Date       time.Time
	Path       string
	PrettyName string
	IsBlogPost bool
	LastMod    time.Time
}

func (p *Page) Dest() string {
	d := strings.TrimPrefix(p.Path, "content/")

	if p.PrettyName != "" {
		d = strings.TrimSuffix(d, filepath.Base(d))
		d += p.PrettyName
	}

	d = strings.TrimSuffix(d, ".md")
	d = strings.TrimSuffix(d, ".html")

	if strings.HasSuffix(d, "_index") {
		d = strings.TrimSuffix(d, "_index")
	}

	d += "/index.html"
	return d
}

func parseFrontMatter(p *Page, content string) string {
	parts := strings.Split(content, "+++")
	lines := strings.Split(parts[1], "\n")[1:]
	content = strings.TrimSpace(parts[2])

	for _, l := range lines {
		l = strings.TrimSpace(l)
		if l == "" {
			continue
		}

		if l == "+++" {
			break
		}

		parts := strings.Split(l, " = ")
		if len(parts) != 2 {
			log.Printf("Warning, invalid front matter in %s: %s", p.Path, l)
			continue
		}

		left := strings.TrimSpace(parts[0])
		right := strings.TrimSpace(parts[1])
		var err error
		switch left {
		case "title":
			p.Title = right[1 : len(right)-1]
		case "date":
			if len(right) == len("2006-01-02 15:04:05") {
				p.Date, err = time.Parse("2006-01-02 15:04:05", right)
			} else {
				p.Date, err = time.Parse("2006-01-02", right)
			}
			if err != nil {
				log.Fatalf("Error parsing date in %s: %s", p.Path, err)
			}
		case "template":
			p.Template = right[1 : len(right)-1]
		default:
			log.Printf("Weird front-matter encountered in file %s: %s\n", p.Path, left)
		}
	}

	return content
}

func process(p *Page) error {
	b, err := os.ReadFile(p.Path) // just pass the file name
	if err != nil {
		return err
	}
	content := string(b)

	if strings.HasPrefix(content, "+++") {
		content = parseFrontMatter(p, content)
	}

	if err := os.MkdirAll("build/"+filepath.Dir(p.Dest()), 0755); err != nil {
		return err
	}

	fh, err := os.Create("build/" + p.Dest())
	if err != nil {
		return err
	}
	defer fh.Close()

	// TODO: Only process Markdown if this is a .md file
	var buf bytes.Buffer
	if err := md.Convert([]byte(content), &buf); err != nil {
		log.Fatalf("error processing markdown: %s", err)
	}

	return templates.Lookup("base.html").Execute(fh, map[string]any{
		"Page":    p,
		"Content": template.HTML(buf.Bytes()),
	})
}

func copyFile(src string, dest string) error {
	info, err := os.Stat(src)
	if err != nil {
		return err
	}

	// if it's a dir, just re-create it in build/
	if info.IsDir() {
		err := os.Mkdir(dest, info.Mode())
		if err != nil && !errors.Is(err, os.ErrExist) {
			return err
		}

		return nil
	}

	// open input
	in, err := os.Open(src)
	if err != nil {
		return err
	}
	defer in.Close()

	// create output
	fh, err := os.Create(dest)
	if err != nil {
		return err
	}
	defer fh.Close()

	// match file permissions
	err = fh.Chmod(info.Mode())
	if err != nil {
		return err
	}

	// copy content
	_, err = io.Copy(fh, in)
	return err
}

func copyDirRecursively(src string, dst string) error {
	return filepath.WalkDir(src, func(path string, d fs.DirEntry, err error) error {
		outpath := dst + strings.TrimPrefix(path, src)
		return copyFile(path, outpath)
	})
}

func readPages() ([]Page, error) {
	pages := make([]Page, 0)
	err := filepath.WalkDir("content", func(path string, d fs.DirEntry, err error) error {
		if d.IsDir() {
			return nil
		}

		info, err := d.Info()
		if err != nil {
			return nil
		}

		p := Page{
			Path:       path,
			IsBlogPost: strings.HasPrefix(path, "content/blog"),
			LastMod:    info.ModTime(),
		}

		// parse date from filename
		filename := filepath.Base(p.Path)
		if len(filename) > 11 && filename[4] == '-' && filename[7] == '-' {
			date, err := time.Parse("2006-01-02", filename[0:len("2000-01-02")])
			if err == nil {
				p.Date = date
				p.PrettyName = filename[11:]
			}
		}

		pages = append(pages, p)
		return nil
	})
	return pages, err
}

func createSitemap(pages []Page) error {
	type Url struct {
		XMLName xml.Name `xml:"url"`
		Loc     string   `xml:"loc"`
		LastMod string   `xml:"lastmod"`
	}

	type Envelope struct {
		XMLName        xml.Name `xml:"urlset"`
		XMLNS          string   `xml:"xmlns,attr"`
		SchemaLocation string   `xml:"xsi:schemaLocation,attr"`
		XSI            string   `xml:"xmlns:xsi,attr"`
		Image          string   `xml:"xmlns:image,attr"`
		Urls           []Url    `xml:""`
	}

	urls := make([]Url, 0, len(pages))
	for _, p := range pages {
		urls = append(urls, Url{
			Loc:     p.Dest(),
			LastMod: p.LastMod.Format(time.RFC3339),
		})
	}

	env := Envelope{
		SchemaLocation: "http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd",
		XMLNS:          "http://www.sitemaps.org/schemas/sitemap/0.9",
		XSI:            "http://www.w3.org/2001/XMLSchema-instance",
		Image:          "http://www.google.com/schemas/sitemap-image/1.1",
		Urls:           urls,
	}

	wr, err := os.Create("build/sitemap.xml")
	if err != nil {
		return err
	}
	enc := xml.NewEncoder(wr)
	wr.WriteString(`<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="//0.0.0.0:8000/sitemap.xsl"?>` + "\n")
	if err := enc.Encode(env); err != nil {
		return err
	}
	wr.Close()

	// copy xml stylesheet
	return copyFile("sitemap.xsl", "build/sitemap.xsl")
}

func main() {
	pages, err := readPages()
	if err != nil {
		log.Fatal(err)
	}

	// build each individual page
	for _, p := range pages {
		if err := process(&p); err != nil {
			log.Printf("Error processing %s: %w\n", p.Path, err)
		}
	}

	if err := createSitemap(pages); err != nil {
		log.Fatalf("Error creating sitemap: %w\n", err)
	}

	// static files
	if err := copyDirRecursively("public", "build"); err != nil {
		log.Fatal(err)
	}

}

// TODO:
// - Go through all files and build a list of pages
// - Then start writing output files, passing the list of posts to each page
