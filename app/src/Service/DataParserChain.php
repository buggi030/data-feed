<?php

namespace App\Service;

class DataParserChain
{
    /* @var DataParserInterface[] */
    private array $parsers = [];

    public function parse(): array
    {
        $objects = [];
        /* @var DataParserInterface $parser */
        foreach ($this->parsers as $parser) {
            $objects = array_merge($objects, $parser->parse());
        }

        return $objects;
    }

    public function addParser(DataParserInterface $parser)
    {
        if (!$parser instanceof DataParserInterface) {
            throw new \InvalidArgumentException(sprintf('parser has to implement %s', DataParserInterface::class));
        }
        $this->parsers[] = $parser;
    }
}
