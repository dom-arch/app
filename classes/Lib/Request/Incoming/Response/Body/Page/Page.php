<?php
namespace Lib\Request\Incoming\Response\Body;

use DOMArch\Assembler;
use DOMArch\Request;
use Lib\Translator;
use DOMArch\View\HTML;

class Page
    extends HTML
{
    protected $_fetcher;
    protected $_translator;
    protected $_urlTranslator;
    protected $_url;

    public function __construct(
        Request $request,
        Translator $translator,
        Translator $url_translator
    )
    {
        $this->_translator = $translator;
        $this->_urlTranslator = $url_translator;
        $this->_url = $request->getUrl();

        parent::__construct();

        $assembler = new Assembler\HTML($this);
        $this->_fetcher = $assembler->getFetcher();
    }

    /**
     * @return mixed
     */
    public function getFetcher()
    {
        return $this->_fetcher;
    }

    public function getTranslator()
    {
        return $this->_translator;
    }

    public function getUrlTranslator()
    {
        return $this->_urlTranslator;
    }

    public function url(
        array $params = [],
        string $fragment = ''
    )
    {
        $url = $this->_url->rewrite($params, $fragment);
        $url->setClassName($url->getClassName());
        $url->setMethod($url->getMethod());
        $url->setModuleName($url->getModuleName());
        $url->setLocale($url->getLocale());
        $url_params = $url->getParams()->toArray();
        ksort($url_params);

        $url->getParams()->clear()->fill($url_params);

        return $url;
    }

    public function websiteUrl(
        array $params = [],
        string $fragment = ''
    )
    {
        return $this->url($params, $fragment)
            ->setSubDomain('');
    }

    public function __toString()
    {
        $content_popup = $this->select('.contentPopup');

        if ($content_popup) {
            $html = (string) $content_popup;
            $memory = memory_get_peak_usage(true) / (1024 * 1024);

            return $html . '<!-- ' . $memory . ' -->' . PHP_EOL;
        }
        
        $html = parent::__toString();
        $memory = memory_get_peak_usage(true) / (1024 * 1024);

        return $html . '<!-- ' . $memory . ' -->' . PHP_EOL;
    }
}
