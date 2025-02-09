# Task Management Test

## Installation Guide

Follow the steps below to set up and run the application.

### 1. Unzip the Project
Extract the contents of the archive:
```sh
unzip TaskManagementTest.zip
```

### 2. Install Dependencies
Navigate to the project directory and install PHP dependencies:
```sh
composer install
```

### 3. Set Up Environment File
Copy the example environment file and update configurations as needed:
```sh
cp .env.example .env
```
Generate the application key:
```sh
php artisan key:generate
```
Configure your database and other settings in the `.env` file.

### 4. Optimize the Application
Run the following command to cache configurations and optimize the application:
```sh
php artisan optimize
```

### 5. Install Frontend Dependencies
Compile frontend assets using npm:
```sh
npm install
npm run dev
```

### 6. Run Tests
Execute the specific test case:
```sh
php artisan test --filter=TaskComponentsTest
```

Your application should now be set up and ready to use!

