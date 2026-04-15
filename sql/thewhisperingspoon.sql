-- The Whispering Spoon Database Schema

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menu items table
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    is_veg BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Preparing', 'Ready', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- Bookings table (for table reservations)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    date DATE NOT NULL,
    time TIME NOT NULL,
    guests INT NOT NULL,
    message TEXT,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- menu items
INSERT INTO menu_items (name, description, price, image, category, is_veg) VALUES
('Manchow', 'Hot and spicy Chinese-style soup.', 199.00, 'soup/manchow.jpg', 'soup', 0),
('Mushroom Cappuccino', 'Earthy mushrooms blended into a creamy soup.', 199.00, 'soup/mushroomcappuccino.jpg', 'soup', 1),
('Cream Of Tomato', 'Classic creamy tomato soup with herbs.', 199.00, 'soup/creamoftomato.jpg', 'soup', 1),
('Hot & Sour', 'Spicy and tangy Indo-Chinese delight.', 199.00, 'soup/hotandsour.jpg', 'soup', 1),
('Ancient Greek With Herb', 'Classic Greek salad with fresh herbs.', 249.00, 'salad/greek.jpg', 'salad', 1),
('Caesar Salad', 'Crunchy lettuce with creamy Caesar dressing.', 249.00, 'salad/caesar.jpg', 'salad', 1),
('Fruit Salad', 'Fresh seasonal fruits with a drizzle of honey.', 299.00, 'salad/fruit.jpg', 'salad', 1),
('Chicken Caesar Salad', 'Grilled chicken over crunchy greens.', 349.00, 'salad/chickencaesar.jpeg', 'salad', 0),
('Chilli Potato', 'Crispy potatoes tossed in spicy sauce.', 299.00, 'nibbles/chillipotato.jpg', 'nibbles', 1),
('Spring Roll', 'Savory, crispy, delightful fried wrap.', 249.00, 'nibbles/springroll.jpg', 'nibbles', 1),
('Garlic Bread', 'Toasted bread with herbed garlic butter.', 259.00, 'nibbles/garlicbread.jpg', 'nibbles', 1),
('Garlic Bread With Cheese', 'Loaded with gooey cheese and garlic flavor.', 289.00, 'nibbles/garliccheese.jpeg', 'nibbles', 1),
('Cheese Sandwich', 'Classic grilled cheese sandwich.', 299.00, 'sandwich/cheese.jpg', 'sandwich', 1),
('Paneer Tikka Sandwich', 'Stuffed with smoky paneer tikka masala.', 349.00, 'sandwich/paneertikka.jpg', 'sandwich', 1),
('Veg Medetarian Sandwich', 'Veggies and herbs packed between bread.', 249.00, 'sandwich/veg.jpg', 'sandwich', 1),
('Smoky Chicken Sandwich', 'Grilled chicken with smokey flavor.', 299.00, 'sandwich/smokychicken.jpeg', 'sandwich', 0),
('Alfredo (Veg)', 'Creamy white sauce pasta with herbs.', 359.00, 'pasta/alfredo.jpg', 'pasta', 1),
('Arrabbiata (Veg)', 'Spicy red tomato-based sauce pasta.', 359.00, 'pasta/arrabbiata.jpg', 'pasta', 1),
('Pesto Pasta', 'Fresh basil pesto mixed with cream pasta.', 399.00, 'pasta/pesto.jpg', 'pasta', 1),
('Margarita', 'Tomato, mozzarella & basil on thin crust.', 299.00, 'pizza/margharita.jpg', 'pizza', 1),
('Farmhouse Veggie', 'Loaded with spicy veggies & capsicum.', 329.00, 'pizza/farmhouse.jpg', 'pizza', 1),
('Paneer Tikka', 'Paneer with roasted onion & bell peppers.', 379.00, 'pizza/paneertikka.jpg', 'pizza', 1),
('Chicken Tikka', 'Chunks of tikka chicken, onions & cheese.', 449.00, 'pizza/chickentikka.jpg', 'pizza', 0),
('Chocolate Lava Cake', 'Warm chocolate cake with a molten center.', 299.00, 'desserts/chocolatelava.jpg', 'desserts', 1),
('Classic Tiramisu', 'Layers of coffee-soaked ladyfingers and mascarpone cream.', 349.00, 'desserts/tiramisu.jpg', 'desserts', 1),
('New York Cheesecake', 'Rich and creamy baked cheesecake with a graham cracker crust.', 329.00, 'desserts/cheesecake.jpeg', 'desserts', 1),
('Brownie Sizzler', 'Hot chocolate brownie with vanilla ice cream on a sizzler plate.', 399.00, 'desserts/browniesizzler.jpg', 'desserts', 1),
('Iced Latte', 'Chilled espresso with milk over ice.', 189.00, 'beverages/icedlatte.jpg', 'beverages', 1),
('Fresh Lime Soda', 'Refreshing lime juice with soda, sweet or salty.', 149.00, 'beverages/freshlimesoda.jpg', 'beverages', 1),
('Traditional Masala Chai', 'Spiced Indian tea, brewed to perfection.', 129.00, 'beverages/masalachai.jpg', 'beverages', 1),
('Virgin Mojito', 'Classic refreshing blend of mint, lime, and soda.', 219.00, 'beverages/virginmojito.jpg', 'beverages', 1),
('Cold Coffee with Ice Cream', 'Blended coffee with milk, sugar, and a scoop of vanilla ice cream.', 249.00, 'beverages/coldcoffee.jpeg', 'beverages', 1);
