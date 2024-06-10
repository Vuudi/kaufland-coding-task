<?php

namespace App\Storage;

use PDO;
use PDOException;
use App\Logger;

class MySQLDatabase implements DatabaseInterface
{
    private PDO $pdo;

    public function connect(): void
    {
        try {
            $dsn = $_ENV['DB_DSN'];
            $user = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];

            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTable();
        } catch (PDOException $e) {
            Logger::log("Connection failed: " . $e->getMessage());
        }
    }

    private function createTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            entity_id INT NOT NULL,
            category_name VARCHAR(255) NOT NULL,
            sku VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            short_description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            link TEXT NOT NULL,
            image TEXT,
            brand VARCHAR(255),
            rating INT,
            caffeine_type VARCHAR(50),
            count INT,
            flavored BOOLEAN,
            seasonal BOOLEAN,
            in_stock BOOLEAN,
            facebook INT,
            is_kcup BOOLEAN
        )");
    }

    public function insertData(array $data): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO items (
            entity_id, category_name, sku, name, description, short_description, price, link, image, brand, rating, caffeine_type, count, flavored, seasonal, in_stock, facebook, is_kcup
        ) VALUES (
            :entity_id, :category_name, :sku, :name, :description, :short_description, :price, :link, :image, :brand, :rating, :caffeine_type, :count, :flavored, :seasonal, :in_stock, :facebook, :is_kcup
        )");

        foreach ($data as $item) {
            $stmt->execute([
                ':entity_id' => $item['entity_id'],
                ':category_name' => $item['category_name'],
                ':sku' => $item['sku'],
                ':name' => $item['name'],
                ':description' => $item['description'],
                ':short_description' => $item['shortdesc'],
                ':price' => $item['price'],
                ':link' => $item['link'],
                ':image' => $item['image'],
                ':brand' => $item['brand'],
                ':rating' => $item['rating'],
                ':caffeine_type' => $item['caffeine_type'],
                ':count' => $item['count'],
                ':flavored' => $item['flavored'],
                ':seasonal' => $item['seasonal'],
                ':in_stock' => $item['instock'],
                ':facebook' => $item['facebook'],
                ':is_kcup' => $item['iskcup']
            ]);
        }
    }
}
