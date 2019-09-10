<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use cebe\markdown\Markdown;

class MarkdownCacheHelper
{
    protected $cache;
    protected $markdown;

    public function __construct(AdapterInterface $cache, Markdown $markdown)
    {
        $this->cache = $cache;
        $this->markdown = $markdown;
    }

    public function parse(string $text) : string
    {
        $item = $this->cache->getItem("markdown" . md5($text));

        if (!$item->isHit()) {
            $item->set($this->markdown->parse($text));
            $this->cache->save($item);
        }

        return $item->get();
    }

}