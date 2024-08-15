# paas-2tierapp
A) Need to run on App Service - PHP
cd D:\home\site\wwwroot
curl -sS https://getcomposer.org/installer | php
php composer.phar require microsoft/azure-storage-blob

B) Check db.php and set all related env variables in App Service

C) Need to run on Azure SQL DB
CREATE TABLE Inquiries (
    id INT PRIMARY KEY IDENTITY(1,1),
    name NVARCHAR(100),
    mobile_number NVARCHAR(20),
    email NVARCHAR(100),
    message NVARCHAR(MAX),
    photo_url NVARCHAR(255),
    created_at DATETIME DEFAULT GETDATE()
);
D) Create storage a/c for eg ntmsphpsa. If any other name usesd for SA, update upload.php
