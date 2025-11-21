<?php

use PHPUnit\Framework\TestCase;
use Bookstore\Cart;
use Bookstore\BookRepository;
use Bookstore\Book;

class CartTest extends TestCase {
    protected function setUp(): void {
        // Reset session for each test
        $_SESSION = [];
    }

    public function testAddBookToCart() {
        $cart = new Cart();
        $cart->add(1, 2);

        $this->assertEquals([1 => 2], $cart->getItems());
    }

    public function testRemoveBookFromCart() {
        $cart = new Cart();
        $cart->add(1, 1);
        $cart->remove(1);

        $this->assertEmpty($cart->getItems());
    }

    public function testUpdateQuantity() {
        $cart = new Cart();
        $cart->add(1, 1);
        $cart->update(1, 5);

        $this->assertEquals([1 => 5], $cart->getItems());
    }

    public function testGetTotal() {
        // Mock BookRepository
        $bookRepo = $this->createMock(BookRepository::class);
        $bookRepo->method('find')->willReturnMap([
            [1, new Book(1, 'Titre 1', 'Auteur 1', 1000, null, null)],
            [2, new Book(2, 'Titre 2', 'Auteur 2', 2000, null, null)],
        ]);

        $cart = new Cart();
        $cart->add(1, 2); // 2 * 1000 = 2000
        $cart->add(2, 1); // 1 * 2000 = 2000

        $this->assertEquals(4000, $cart->getTotal($bookRepo));
    }
}
