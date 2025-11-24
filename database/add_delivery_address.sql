-- Ajouter le champ delivery_address à la table orders
ALTER TABLE orders 
ADD COLUMN delivery_address VARCHAR(500) NOT NULL AFTER customer_phone;
