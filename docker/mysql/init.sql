CREATE DATABASE IF NOT EXISTS registro_comision_film CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'rcf_user'@'%' IDENTIFIED BY 'dev_rcf_pass_2025';
GRANT ALL PRIVILEGES ON registro_comision_film.* TO 'rcf_user'@'%';
FLUSH PRIVILEGES;
