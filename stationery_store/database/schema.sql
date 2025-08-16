-- Stationery Store schema

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  image_url VARCHAR(500) DEFAULT NULL,
  stock INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS wishlists (
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed categories
INSERT INTO categories (name, slug) VALUES
('Pens & Writing', 'pens-writing'),
('Notebooks', 'notebooks'),
('Office Supplies', 'office-supplies'),
('Art & Craft', 'art-craft')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Seed products
INSERT INTO products (category_id, name, slug, description, price, image_url, stock) VALUES
(1, 'Premium Gel Pen', 'premium-gel-pen', 'Smooth writing gel pen with 0.5mm tip.', 2.99, 'https://images.unsplash.com/photo-1515876300841-1f3a5d1aa36e?w=600', 200),
(1, 'Fountain Pen', 'fountain-pen', 'Elegant fountain pen with refillable converter.', 24.99, 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=600', 50),
(2, 'A5 Dotted Notebook', 'a5-dotted-notebook', '160 pages, 100gsm paper, perfect for bullet journaling.', 9.99, 'https://images.unsplash.com/photo-1520975922284-9e0ce82764b8?w=600', 120),
(3, 'Desk Organizer', 'desk-organizer', 'Keep your desk tidy with multiple compartments.', 14.50, 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?w=600', 75),
(4, 'Watercolor Set', 'watercolor-set', '24-color watercolor set with brush.', 19.00, 'https://images.unsplash.com/photo-1501876725168-00c445821c9e?w=600', 60)
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price), description = VALUES(description), image_url = VALUES(image_url), stock = VALUES(stock), category_id = VALUES(category_id);