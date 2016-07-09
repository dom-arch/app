<?php
namespace Assemblies\HTML\Components;

use DOMArch\Assembly;
use Lib\Url;
use Lib\View\HTML\Page as HTMLPage;

class Nav
    extends Assembly
{
    const
        LINKS = [
            [
                'moduleName' => 'Contact'
            ]
        ];

    public static function assemble(
        HTMLPage $page
    )
    {
        $document = $page->getDocument();
        $fetcher = $page->getFetcher();

        $header = $document->select('header');
        $nav = $fetcher->component()->nav()->fetch();

        $header->append($nav);

        $assembly = new static($nav);

        $assembly->appendLinks($page);
        $assembly->appendLanguageLinks($page);
        $assembly->translate($nav);

        return $assembly;
    }

    public function appendLinks(
        HTMLPage $page
    )
    {
        $document = $page->getDocument();
        $ol = $this->getNode()->select('ol');

        foreach (self::LINKS as $url_params) {
            $anchor_url = $document->url($url_params);

            $text = implode('-', [
                $anchor_url->getModuleName(),
                $anchor_url->getClassName()
            ]);

            $li = $ol->append([
                'tag' => 'li',
                'children' => [
                    [
                        'tag' => 'a',
                        'data' => $text,
                        'attributes' => [
                            'href' => $anchor_url
                        ]
                    ]
                ]
            ]);
        }

        return $this;
    }

    public function appendLanguageLinks(
        HTMLPage $page
    )
    {
        $url = $page->getUrl();
        $ol = $this->getNode()->select('#languages');
        $alternates = $url->getAlternates();
        $format = $url->getFormat();
        $locale = $url->getLocale();

        $anchor_urls = [
            $locale => $url
        ] + $alternates;

        foreach ($anchor_urls as $locale => $anchor_url) {
            $ol->append([
                'tag' => 'li',
                'children' => [
                    [
                        'tag' => 'a',
                        'data' => strtoupper($locale),
                        'attributes' => [
                            'href' => $anchor_url,
                            'hreflang' => $locale,
                            'lang' => $locale,
                            'title' => $format
                        ]
                    ]
                ]
            ]);
        }

        return $this;
    }

    public function translate()
    {
        $nav = $this->getNode();

        $this->_translate($nav);
        $this->_translateAttr($nav, 'href');
        $this->_translateAttr($nav, 'title');

        return $this;
    }
}