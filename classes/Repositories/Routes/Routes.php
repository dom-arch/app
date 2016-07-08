<?php

namespace Repositories;

use Indoctrinated\Repository;
use Lib\Config;
use Lib\Url;
use Repositories\Routes\Bundle;
use Routes as Entity;

/**
 * Routes
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Routes extends Repository
{
    protected static $_bundle;

    public static function bundle()
    {
        if (static::$_bundle) {
            return static::$_bundle;
        }

        static::$_bundle = new Bundle();

        return static::$_bundle;
    }

    public static function parse(
        Url $url,
        string $method
    )
    {
        $common_config = Config::global()->get('common');
        $key = $common_config->get('encryptionKey');
        $locales = $common_config->get('locales')->toArray();
        $url_string = (string) $url->decrypt($key);

        list($format, $params) = static::_getFormat($url_string);

        $route = static::_getRoute($format, $locales);

        if (!$route) {
            return;
        }

        return static::_getUrl($route, $url_string, $method, $locales, $params);
    }

    protected static function _getFormat(
        string $url
    )
    {
        $params = [];
        $counter = 0;

        $callback = function($matches)
        use (&$params, &$counter) {
            $counter += 1;
            $sprintf_params[] = $matches[2];

            return '%' . $counter . '$s';
        };

        $format = preg_replace_callback(
            '/(-\()([^)]+)(\)-?)/', $callback, $url
        );

        return [$format, $params];
    }

    protected static function _getRoute(
        string $translation,
        array $locales
    )
    {
        $conditions = [];

        foreach ($locales as $locale) {
            $conditions[] = 'route.' . $locale . ' = :translation';
        }

        return Entity::getEntityRepository()
            ->createQueryBuilder('route')
            ->andWhere(implode(' OR ', $conditions))
            ->andWhere('route.archivedAt IS NULL')
            ->setParameters([
                'translation' => $translation
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    protected static function _getUrl(
        Entity $route,
        string $requested_url,
        string $method,
        array $locales,
        array $params
    )
    {
        $format = $route->getFormat();
        $resolved_url = Url::parse(sprintf($format, ...$params));

        if ($resolved_url->getMethod() !== $method) {
            return;
        }

        $key = Config::global()
            ->get('common')
            ->get('encryptionKey');

        $resolved_url->setFormat($format);
        
        $data = $route->toArray();

        foreach ($locales as $locale) {
            $translation = sprintf($data[$locale], ...$params);

            $encrypted = Url::parse($translation)
                ->setMethod('get')
                ->encrypt($key);

            if ($translation === $requested_url && !$resolved_url->getCanonical()) {
                $resolved_url->setLocale($locale);
                $resolved_url->setCanonical($encrypted);
            } else {
                $resolved_url->addAlternate($locale, $encrypted);
            }
        }

        return $resolved_url;
    }

    public static function archive(
        Url $url,
        string $method = 'get'
    )
    {
        $url_string = Url::parse((string) $url)
            ->setMethod($method)
            ->setLocale(null);

        list($format) = static::_getFormat((string) $url_string);

        $route = Entity::getEntityRepository()
            ->selectBy([
                'format' => $format
            ]);

        if ($route) {
            $route->archive();
        }
    }
}
