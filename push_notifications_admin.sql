-- Allow admins in Push Subscriptions
ALTER TABLE `push_subscriptions`
MODIFY COLUMN `user_type` ENUM('user', 'specialist', 'administrator') NOT NULL DEFAULT 'user';

-- Allow admins in Preferences
ALTER TABLE `user_notification_preferences`
MODIFY COLUMN `user_type` ENUM('user', 'specialist', 'administrator') NOT NULL DEFAULT 'user';
