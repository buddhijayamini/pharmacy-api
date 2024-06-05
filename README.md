Pharmacy API 

Description -
A pharmacy with the stakeholders involved are the owner, manager and cashier requires a system to streamline its business processes, involving authentication, medication inventory management, and customer record management. The system needs to enforce user roles and permissions for different actions.

Setup Instructions
Prerequisites
Make sure you have the following installed on your system:

PHP (>= 7.3)
Composer
MySQL
Installation
Clone the repository:

bash
Copy code
git clone https://github.com/username/project.git
Navigate to the project directory:

bash
Copy code
cd project
Install PHP dependencies:

bash
Copy code
composer install
Install the Spatie\Permission library:

bash
Copy code
composer require spatie/laravel-permission
Copy the .env.example file to .env:

bash
Copy code
cp .env.example .env
Generate the application key:

bash
Copy code
php artisan key:generate
Set up your database in the .env file:

bash
Copy code
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
Run the database migrations:

bash
Copy code
php artisan migrate
Seed the database with roles and permissions:

bash
Copy code
php artisan db:seed --class=RolesAndPermissionsSeeder
Running the Application
To start the development server, run:

bash
Copy code
php artisan serve
The application will be accessible at http://localhost:8000 by default.

Usage
Once the application is set up, you can manage roles and permissions in the database or via the provided UI (if implemented). Make sure to assign roles to users as needed to control access to different parts of the application.

Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

License
MIT

Extra Docs -
  inside docs folder - DB , ERD , API collection
