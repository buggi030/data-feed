<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\XmlEncoder;

class XMLArrayEncoder extends XmlEncoder
{
    public function decode(string $data, string $format, array $context = []): mixed
    {
        $data = parent::decode($data, $format, $context);
        $items = [];
        foreach ($data as $key => $value) {
            if (isset($value['entity_id'])) {
                $items[] = $value;
                continue;
            }
            $items = array_merge($items, $value);
        }

        return $items;
    }
}
