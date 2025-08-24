<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\ProductApiController;
use App\Domain\Repository\ProductRepository;
use App\Domain\Model\ProductModel;
use App\Domain\Enum\Category;
use Tests\Helpers\Output;

final class ProductApiControllerTest extends TestCase
{
    /** @return ProductRepository */
    private function fakeRepo(): ProductRepository
    {
        return new class implements ProductRepository {
            private array $items = [];
            public function __construct() {
                $this->items = [
                    new ProductModel(id: 'p1', name: 'Pão',   category: Category::FOOD,     basePriceCents: 600,  isIced: null),
                    new ProductModel(id: 'p2', name: 'Latte', category: Category::BEVERAGE, basePriceCents: 1250, isIced: true),
                ];
            }
            public function all(): array { return $this->items; }
            public function find(string $id): ?ProductModel {
                foreach ($this->items as $i) if ($i->id === $id) return $i;
                return null;
            }
            public function create(ProductModel $p): ProductModel {
                $this->items[] = $p; return $p;
            }
        };
    }

    public function testIndexReturnsProductList(): void
    {
        $c = new ProductApiController($this->fakeRepo());
        $res = Output::capture(fn() => $c->index());
        $this->assertSame(200, $res['status']);
        $this->assertIsArray($res['json']);
        $this->assertArrayHasKey('data', $res['json']);
        $this->assertCount(2, $res['json']['data']);
    }

    public function testShowNotFound(): void
    {
        $c = new ProductApiController($this->fakeRepo());
        $res = Output::capture(fn() => $c->show('missing'));
        $this->assertSame(404, $res['status']);
        $this->assertArrayHasKey('error', $res['json']);
    }

    public function testStoreWithoutBodyReturns422(): void
    {
        $c = new ProductApiController($this->fakeRepo());
        $res = Output::capture(fn() => $c->store()); // sem body -> inválido
        $this->assertSame(422, $res['status']);
        $this->assertArrayHasKey('error', $res['json']);
    }
}
