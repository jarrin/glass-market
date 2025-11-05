-- Update existing unverified users to allow them to login
-- Run this in PHPMyAdmin after making the registration changes

-- 1. Check how many users are currently unverified (excluding admin)
SELECT 
    id, 
    name, 
    email, 
    email_verified_at,
    created_at
FROM users 
WHERE email_verified_at IS NULL 
AND email != 'admin@glassmarket.com';

-- 2. Update all unverified users (except admin) to be verified
UPDATE users 
SET email_verified_at = NOW() 
WHERE email_verified_at IS NULL 
AND email != 'admin@glassmarket.com';

-- 3. Verify the update - should show all users are now verified (except possibly admin)
SELECT 
    id, 
    name, 
    email, 
    email_verified_at,
    CASE 
        WHEN email_verified_at IS NULL THEN 'NOT VERIFIED'
        ELSE 'VERIFIED'
    END as status
FROM users 
ORDER BY id;

-- 4. Show count of verified vs unverified
SELECT 
    CASE 
        WHEN email_verified_at IS NULL THEN 'Unverified'
        ELSE 'Verified'
    END as verification_status,
    COUNT(*) as count
FROM users
GROUP BY verification_status;
