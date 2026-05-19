-- Insert default Super Admin account (password: SuperAdmin@123)
-- Only inserts if no super_admin exists yet
INSERT INTO users (name, email, username, password, role, status, created_at)
SELECT
  'Super Administrator',
  'superadmin@gobuff.com',
  'superadmin',
  '$2y$12$n5bhAWLVJ6h9zIJq1fsX0OtHN9RLLMImFgSgVhLB3B0FNsgJk3cUG',
  'super_admin',
  'active',
  NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM users WHERE role = 'super_admin'
);
