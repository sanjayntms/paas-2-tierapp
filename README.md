# paas-2tierapp

# A) Need to run on App Service - PHP, SSH APP service from app service blade
cd D:\home\site\wwwroot
curl -sS https://getcomposer.org/installer | php
php composer.phar require microsoft/azure-storage-blob

# B) Check db.php and set all related env variables in App Service


# C) Need to run on Azure SQL DB
CREATE TABLE Inquiries (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(100),
    mobile_number NVARCHAR(20),
    email NVARCHAR(100),
    message NVARCHAR(MAX),
    photo_url NVARCHAR(255),
    created_at DATETIME DEFAULT GETDATE()
);

# D)CREATE TABLE Users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    username NVARCHAR(255) NOT NULL,
    password_hash NVARCHAR(255) NOT NULL
);
INSERT INTO Users (username, password_hash) 
VALUES ('admin', 'admin123');

# E) Create storage account and add container inquiry-photos

