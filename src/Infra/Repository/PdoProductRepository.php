<?php
declare(strict_types=1);

namespace App\Infra\Repository;

use App\Domain\Model\ProductModel;
use App\Domain\Repository\ProductRepository;
use App\Infra\Database;

final class PdoProductRepository implements ProductRepository
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM products ORDER BY created_at DESC');
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => ProductModel::fromRow($r), $rows);
    }

    public function find(string $id): ?ProductModel
    {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? ProductModel::fromRow($row) : null;
    }

    public function create(ProductModel $p): ProductModel
    {
        $sql = 'INSERT INTO products (id, name, category, base_price_cents, is_iced, created_at)
                VALUES (:id, :name, :category, :base_price_cents, :is_iced, :created_at)';
        $stmt = $this->pdo->prepare($sql);
        $id = $p->id ?: bin2hex(random_bytes(16));

        $stmt->execute([
            'id' => $id,
            'name' => $p->name,
            'category' => $p->category->value,
            'base_price_cents' => $p->basePriceCents,
            'is_iced' => $p->isIced ? 1 : 0,
            'created_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);

        return $this->find($id) ?? $p;
    }
}
