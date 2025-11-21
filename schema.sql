-- Create database if not exists (optional, usually done manually)
-- CREATE DATABASE IF NOT EXISTS bookstore;
-- USE bookstore;

-- Table: books
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    payment_method ENUM('wave', 'om', 'cod') NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: order_items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- Insert some sample data
INSERT INTO books (title, author, price, image_url, description) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupéry', 5000.00, 'https://covers.openlibrary.org/b/id/12605387-L.jpg', 'Un classique de la littérature française.'),
('L''Étranger', 'Albert Camus', 4500.00, 'https://covers.openlibrary.org/b/id/12653056-L.jpg', 'Un roman philosophique.'),
('Une si longue lettre', 'Mariama Bâ', 3500.00, 'https://covers.openlibrary.org/b/id/10584307-L.jpg', 'Un roman épistolaire majeur de la littérature africaine.'),
('Harry Potter à l''école des sorciers', 'J.K. Rowling', 8000.00, 'https://covers.openlibrary.org/b/id/10522666-L.jpg', 'Le début de la saga magique.'),
('Clean Code', 'Robert C. Martin', 25000.00, 'https://covers.openlibrary.org/b/id/12539642-L.jpg', 'Le guide pour écrire du code propre.');
