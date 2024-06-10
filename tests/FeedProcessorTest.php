<?php

use PHPUnit\Framework\TestCase;
use App\FeedProcessor;
use App\Storage\MySQLDatabase;

class FeedProcessorTest extends TestCase
{
    private ?PDO $pdo;
    private MySQLDatabase $database;

    protected function setUp(): void
    {
        $dsn = 'mysql:host=localhost;dbname=feed_db';
        $user = 'kaufland';
        $password = 'kaufland';

        try {
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->fail('Connection failed: ' . $e->getMessage());
        }

        $_ENV['DB_DSN'] = $dsn;
        $_ENV['DB_USER'] = $user;
        $_ENV['DB_PASS'] = $password;

        $this->database = new MySQLDatabase();
        $this->database->connect();
        $this->createTable();
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
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

    private function clearTable(): void
    {
        $this->pdo->exec("TRUNCATE TABLE items");
    }

    public function testProcess()
    {
        $this->clearTable();

        $processor = new FeedProcessor($this->database);
        $processor->process(__DIR__ . '/sample.xml');

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM items");
        $count = $stmt->fetchColumn();

        $this->assertEquals(5, $count, "The number of inserted items should be 5");

        $stmt = $this->pdo->query("SELECT * FROM items WHERE entity_id = 340");
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Green Mountain Ground Coffee', $item['category_name']);
        $this->assertEquals('20', $item['sku']);
        $this->assertEquals('Green Mountain Coffee French Roast Ground Coffee 24 2.2oz Bag', $item['name']);
        $this->assertEquals('', $item['description']);
        $this->assertEquals(41.60, $item['price']);
        $this->assertEquals('http://www.coffeeforless.com/green-mountain-coffee-french-roast-ground-coffee-24-2-2oz-bag.html', $item['link']);
    }
}
