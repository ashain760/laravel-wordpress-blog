# Laravel WordPress Blog Application

| ![blog](https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR0ue6TObmzWAd2-zDG7m6cAyiqOzersVMrzA&s) | ![blog](https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSa2PnnK6xtqj4NAepcXAKWR8B3gQxS_xbM2g&s) |
|-------------|--------------|

This is a Laravel + Vue based WordPress Blog Management back office that allows admins to create, edit, delete, and manage blog posts. It supports WordPress integration for authentication.

---

## Features

- WordPress Admin login
- CRUD operations for blog posts
- Post priority management
- Draft & Publish status
- Search posts by title
- User-friendly Vuetify front-end
- Web & API laravel backoffice

---

## Requirements

- PHP >= 8.1
- Composer
- MySQL
- Node.js & npm (for front-end assets)
- Git

---

## Usage

### Clone the repository:

```bash
git clone https://github.com/ashain760/laravel-wordpress-blog.git
```

### Install PHP dependencies:

```
composer install
```

### Install front-end dependencies:

```
npm install
npm run build
```

### Copy .env.example to .env:

```
cp .env.example .env
```

### Generate application key

```
php artisan key:generate
```

### Set your environment variables in .env

```
DB_HOST=<your_database_host>
DB_PORT=3306
DB_DATABASE=<your_database_name>
DB_USERNAME=<your_database_user>
DB_PASSWORD=<your_database_password>
```

### Run MySql database migrations

```
php artisan migrate:fresh
```

### Running Locally

```
php artisan serve
```

1. Display login screen

![blog](public/screenshots/1.jpg)

2. After click "Sign in with WordPress" it redirects to WordPress login

![blog](public/screenshots/2.jpg)

##### Sample ADMIN WordPress account login details (wordpress.com):

Username:
```
jamesanderson755698
```
Password:
```
RvUCa1NP@LvtOfwqp0X0KgQ*
```

##### Sample ADMIN Gmail account login details:

Username:
```
james.anderson755698@gmail.com
```
Password:
```
james@123
```
3. After logged in, it redirects to user approval section. click "Approve"

![blog](public/screenshots/3.jpg)

4. After approval & login success it redirects to blog post page

![blog](public/screenshots/4.jpg)

![blog](public/screenshots/5.jpg)

5. In blog post page you can add edit delete sync search blogs

##### Create blogs

![blog](public/screenshots/6.jpg)

![blog](public/screenshots/7.jpg)

![blog](public/screenshots/8.jpg)

![blog](public/screenshots/9.jpg)

##### Update blogs

![blog](public/screenshots/10.jpg)

![blog](public/screenshots/11.jpg)

![blog](public/screenshots/12.jpg)

![blog](public/screenshots/13.jpg)

##### Add priority blogs

![blog](public/screenshots/14.jpg)

![blog](public/screenshots/15.jpg)

##### Delete blogs

![blog](public/screenshots/16.jpg)

![blog](public/screenshots/17.jpg)

![blog](public/screenshots/18.jpg)

![blog](public/screenshots/19.jpg)

##### Search blogs

![blog](public/screenshots/20.jpg)
