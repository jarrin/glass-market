-- Update existing listings to set user_id based on company ownership
-- This ensures old listings have a contact email

UPDATE listings l
INNER JOIN companies c ON l.company_id = c.id
SET l.user_id = c.owner_user_id
WHERE l.user_id IS NULL AND l.company_id IS NOT NULL;

-- For any remaining listings without company, set to admin user (id 4)
UPDATE listings 
SET user_id = 4 
WHERE user_id IS NULL;
