<?php

namespace App;

use SimpleXMLElement;
use App\Storage\DatabaseInterface;

readonly class FeedProcessor
{

    public function __construct(private DatabaseInterface $database)
    {
        $this->database->connect();
    }

    public function process($xmlFile): void
    {
        if (!file_exists($xmlFile)) {
            Logger::log("File not found: " . $xmlFile);
            return;
        }

        try {
            $xml = new SimpleXMLElement(file_get_contents($xmlFile));
            $data = [];
            foreach ($xml->item as $item) {
                $data[] = [
                    'entity_id' => (int)$item->entity_id,
                    'category_name' => (string)$item->CategoryName,
                    'sku' => (string)$item->sku,
                    'name' => (string)$item->name,
                    'description' => (string)$item->description,
                    'shortdesc' => (string)$item->shortdesc,
                    'price' => (float)$item->price,
                    'link' => (string)$item->link,
                    'image' => (string)$item->image,
                    'brand' => (string)$item->Brand,
                    'rating' => (int)$item->Rating,
                    'caffeine_type' => (string)$item->CaffeineType,
                    'count' => (int)$item->Count,
                    'flavored' => (string)$item->Flavored === 'Yes' ? 1 : 0,
                    'seasonal' => (string)$item->Seasonal === 'Yes' ? 1 : 0,
                    'instock' => (string)$item->Instock === 'Yes' ? 1 : 0,
                    'facebook' => (int)$item->Facebook,
                    'iskcup' => (string)$item->IsKCup === 'Yes' ? 1 : 0
                ];
            }
            $this->database->insertData($data);
        } catch (\Exception $e) {
            Logger::log("Processing failed: " . $e->getMessage());
        }
    }
}
