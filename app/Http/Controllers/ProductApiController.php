<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Repository\ProductRepository;
use App\Domain\Model\ProductModel;
use App\Domain\Enum\Category;

final class ProductApiController extends BaseController
{
    public function __construct(private ProductRepository $repo) {}

    public function index(): void
    {
        $this->json(['data' => array_map(fn(ProductModel $p) => $p->toArray(), $this->repo->all())]);
    }

    public function show(string $id): void
    {
        $p = $this->repo->find($id);
        if (!$p) { $this->json(['error' => 'Produto não encontrado'], 404); return; }
        $this->json($p->toArray());
    }

    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input') ?: '', true) ?? [];
        $name = trim((string)($input['name'] ?? ''));
        $category = (string)($input['category'] ?? '');
        $base = (float)($input['base_price'] ?? 0);
        $isIced = array_key_exists('is_iced', $input) ? (bool)$input['is_iced'] : null;

        if ($name === '' || !in_array($category, ['food','beverage'], true) || $base <= 0) {
            $this->json(['error' => 'Parâmetros inválidos'], 422); return;
        }

        $model = new ProductModel(
            id: bin2hex(random_bytes(16)),
            name: $name,
            category: Category::from($category),
            basePriceCents: (int) round($base * 100),
            isIced: $isIced
        );

        $created = $this->repo->create($model);
        $this->json($created->toArray(), 201);
    }
}
