-- Test query to check subscriptions for test_sub user
-- Run this in PHPMyAdmin to see what's in the database

-- First, find test_sub's user ID
SELECT id, name, email, company_id 
FROM users 
WHERE email = 'test_sub@testsub.com';

-- Then check subscriptions for that user (should be user_id = 10)
SELECT * 
FROM user_subscriptions 
WHERE user_id = 10
ORDER BY created_at DESC;

-- Also check what subscriptions exist in total
SELECT 
    us.*,
    u.name as user_name,
    u.email as user_email
FROM user_subscriptions us
LEFT JOIN users u ON us.user_id = u.id
ORDER BY us.created_at DESC;
