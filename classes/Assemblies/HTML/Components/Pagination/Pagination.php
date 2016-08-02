<?php
namespace Assemblies\HTML\Components;

use DOMArch\Assembly;
use Lib\Request\Incoming;
use Lib\View\HTML\Page;

abstract class Pagination
    extends Assembly
{
    protected static $_items = 10;
    protected static $_limit = 5;
    protected static $_fields = [];
    protected $_page;

    public static function assemble(
        Page $page,
        string $parent_selector,
        int $total,
        array $lines
    )
    {
        $document = $page->getDocument();
        $fetcher = $page->getFetcher();
        $url_params = Incoming::current()->getUrl()->getParams();
        $current = (int) $url_params->get('page', 1);
        $table = $fetcher->component()->pagination()->fetch();
        $container = $document->select($parent_selector);
        $container->append($table);
        $pagination = new static($table);
        $fields = static::$_fields;

        $pagination->_page = $page;
        $pagination->_writeHeads($table->select('thead tr'), $fields);
        $pagination->_writeLines($table->select('tbody'), $fields, $lines);
        $pagination->_writePages($table->select('tfoot td'), $total, $current);

        return $pagination;
    }

    private function _getPages(
        int $total,
        int $current = 1
    )
    {
        $limit = static::$_limit;
        $items = static::$_items;
        $result = [];

        if ($total && $items) {
            $result = range(1, ceil($total / $items));

            if ($current && $limit) {
                if (($limit = floor($limit / 2) * 2 + 1) >= 1) {
                    $min = min(count($result) - $limit, $current - ceil($limit / 2));
                    $max = max(0, $min);
                    $result = array_slice($result, $max, $limit);
                }
            }
        }

        return $result;
    }

    private function _writeHeads(
        $container,
        $fields
    )
    {
        foreach ($fields as $field) {
            $container->append([
                'tag' => 'th',
                'children' => [
                    [
                        'tag' => 'a',
                        'data' => $field,
                        'attributes' => [
                            'href' => $this->_getHeadUrl($field)
                        ]
                    ]
                ]
            ]);
        }

        $this->_translate($container);
        $this->_translateAttr($container, 'href');
    }

    private function _writeLines(
        $container,
        $fields,
        $lines
    )
    {
        $document = $container->ownerDocument;

        foreach ($lines as $line) {
            $tr = $document->create([
                'tag' => 'tr'
            ]);

            $container->append($tr);
            $tr->dataset->href = $this->_getLineUrl($line->id);

            foreach ($fields as $field) {
                $tr->append([
                    'tag' => 'td',
                    'data' => $line->{$field} ?? ''
                ]);
            }
        }

        $this->_translateAttr($container, 'data-href');
    }

    private function _writePages(
        $container,
        $total,
        $current
    )
    {
        foreach ($this->_getPages($total, $current) as $number) {
            $attributes = [
                'href' => $this->_getPaginationUrl($number)
            ];

            if ($current === $number) {
                $attributes['class'] = 'current';
            }

            $container->append([
                'tag' => 'a',
                'data' => $number,
                'attributes' => $attributes
            ]);
        }

        $this->_translateAttr($container, 'href');
    }

    abstract protected function _getHeadUrl(
        string $name
    );

    abstract protected function _getLineUrl(
        int $id
    );

    abstract protected function _getPaginationUrl(
        int $number
    );
}
