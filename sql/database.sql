CREATE DATABASE IF NOT EXISTS ar_furniture CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ar_furniture;

CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(120) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  slug VARCHAR(160) NOT NULL UNIQUE,
  name VARCHAR(160) NOT NULL,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  currency VARCHAR(8) NOT NULL DEFAULT 'RM',
  glb_path VARCHAR(255) NOT NULL,
  usdz_path VARCHAR(255) NULL,
  thumb_path VARCHAR(255) NULL,
  width_cm DECIMAL(8,2) NULL,
  height_cm DECIMAL(8,2) NULL,
  depth_cm DECIMAL(8,2) NULL,
  glb_x_m DECIMAL(8,4) NULL,
  glb_y_m DECIMAL(8,4) NULL,
  glb_z_m DECIMAL(8,4) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_products_active_sort (is_active, sort_order)
) ENGINE=InnoDB;

INSERT INTO admins (username, password_hash) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro0m3iY0G1HfWg4g7KfN2m');
INSERT INTO categories (slug, name, sort_order) VALUES
('chairs', 'Chairs', 1), ('sofas', 'Sofas', 2), ('tables', 'Tables', 3), ('beds', 'Beds', 4), ('storage', 'Storage', 5), ('lighting', 'Lighting', 6);
INSERT INTO products (category_id, slug, name, description, price, currency, glb_path, width_cm, height_cm, depth_cm, sort_order) VALUES
(1, 'modern-armchair', 'Modern Armchair', 'A tailored lounge chair with a calm silhouette for reading corners and quiet rooms.', 899.00, 'RM', 'models/modern-armchair.glb', 78, 82, 76, 1),
(3, 'arc-coffee-table', 'Arc Coffee Table', 'A compact coffee table with a softened edge and a warm timber finish.', 649.00, 'RM', 'models/arc-coffee-table.glb', 110, 42, 60, 2),
(6, 'halo-floor-lamp', 'Halo Floor Lamp', 'A sculptural floor lamp that brings a soft pool of light to the room.', 429.00, 'RM', 'models/halo-floor-lamp.glb', 35, 152, 35, 3);
