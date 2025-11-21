-- Add a temporary auto-increment column
ALTER TABLE staff ADD COLUMN new_emp_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

-- Copy the auto-incremented values to the original emp_id column
UPDATE staff SET emp_id = new_emp_id;

-- Drop the temporary column
ALTER TABLE staff DROP COLUMN new_emp_id;

-- Now modify emp_id to be auto-incrementing primary key
ALTER TABLE staff MODIFY emp_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY; 