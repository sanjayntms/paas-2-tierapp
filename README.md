# NTMS Azure PaaS-2tierapp 
## 
This project demonstrates a 2-tier application using PHP with Azure App Service, Azure SQL Database, and Azure Blob Storage.
# A) Need to run on App Service - PHP, SSH APP service from app service blade
        cd site/wwwroot
        curl -sS https://getcomposer.org/installer | php
        php composer.phar require microsoft/azure-storage-blob

# B) Check db.php and set all related env variables in App Service


# C) Need to run on Azure SQL DB - Create Table for inquiries
## 
    CREATE TABLE Inquiries (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(100),
    mobile_number NVARCHAR(20),
    email NVARCHAR(100),
    message NVARCHAR(MAX),
    photo_url NVARCHAR(255),
    created_at DATETIME DEFAULT GETDATE()
                          );

# D) Create Azure SQL table usesr for login
    CREATE TABLE Users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    username NVARCHAR(255) NOT NULL,
    password_hash NVARCHAR(255) NOT NULL
                         );
    INSERT INTO Users (username, password_hash) 
    VALUES ('admin', 'admin123');

# E) Create storage account and add container inquiry-photos
# F) For SQL injection, use ' OR '1'='1'
