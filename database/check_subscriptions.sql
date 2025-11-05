-- Quick check for subscription issues
-- Run this in PHPMyAdmin to see what's going on

-- 1. Show all users
SELECT id, name, email, created_at
FROM users
ORDER BY id;

-- 2. Show all subscriptions
SELECT 
    us.id as subscription_id,
    us.user_id,
    u.name as user_name,
    u.email as user_email,
    us.start_date,
    us.end_date,
    us.is_active,
    us.is_trial,
    CASE 
        WHEN us.end_date < CURDATE() THEN 'EXPIRED'
        WHEN us.is_active = 1 THEN 'ACTIVE'
        ELSE 'CANCELLED'
    END as status,
    DATEDIFF(us.end_date, CURDATE()) as days_remaining
FROM user_subscriptions us
LEFT JOIN users u ON us.user_id = u.id
ORDER BY us.created_at DESC;

-- 3. Count subscriptions per user
SELECT 
    u.id,
    u.name,
    u.email,
    COUNT(us.id) as subscription_count,
    SUM(CASE WHEN us.is_active = 1 AND us.end_date >= CURDATE() THEN 1 ELSE 0 END) as active_count
FROM users u
LEFT JOIN user_subscriptions us ON u.id = us.user_id
GROUP BY u.id, u.name, u.email
HAVING subscription_count > 0
ORDER BY u.id;
