-- Script om een gebruiker admin te maken
-- Vervang 'your_email@example.com' met jouw email adres

-- Maak jezelf admin
UPDATE users 
SET is_admin = 1 
WHERE email = 'colinpoort@hotmail.com';

-- Controleer of het gelukt is
SELECT id, name, email, is_admin 
FROM users 
WHERE email = 'colinpoort@hotmail.com';
