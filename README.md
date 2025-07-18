# Blog Management System API

This is a backend API for a Blog Management System built with Laravel 12 and PHP 8. It allows users to authenticate, create, read, update, and delete blogs, as well as like/unlike blog posts.

## Features

* **User Authentication:** Secure login/logout using Laravel Sanctum for API token generation.
* **Blog CRUD Operations:**
    * Create new blog posts with a title, description, and an optional image.
    * View a list of all blogs with pagination.
    * Filter blogs by "most liked" and "latest added."
    * Search blogs by title and description.
    * Edit existing blog posts (only by the owner).
    * Delete blog posts (only by the owner).
* **Like/Unlike Functionality:** Users can like and unlike blog posts (toggle functionality).
* **Polymorphic Relationships:** Implemented for the liking feature to allow extensibility for liking other entities in the future.
* **Image Uploads:** Supports image uploads for blog posts.
* **Database Migrations & Seeders:** Database schema managed via Laravel migrations, with seeders for initial dummy data.

## Technologies Used

* **PHP:** 8.x
* **Laravel Framework:** 12.x
* **Database:** MySQL (configured in `.env`)
* **Authentication:** Laravel Sanctum
* **Development Environment:** VS Code, Linux

## Setup Instructions

Follow these steps to get the project up and running on your local machine.

### Prerequisites

* PHP 8.x or higher
* Composer
* MySQL Server

### Installation Steps

1.  **Clone the Repository:**
    ```bash
    git clone https://github.com/ashna0204/blog-management-system.git
    cd blog-management-system
    ```

2.  **Install Composer Dependencies:**
    ```bash
    composer install
    ```

3.  **Create `.env` File:**
    Copy the example environment file and generate an application key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Configure Database:**
    Open the `.env` file and update your database connection details:
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=blog_db         # Your database name
    DB_USERNAME=root            # Your database username
    DB_PASSWORD=                # Your database password
    ```
    Make sure to create the `blog_db` database in your MySQL server.

5.  **Run Migrations and Seed Database:**
    This command will create all necessary tables and populate them with some dummy data (users and blogs).
    ```bash
    php artisan migrate:fresh --seed
    ```
    * **Note:** A default user is created with `email: test@example.com` and `password: password`.

6.  **Link Storage (for image uploads):**
    ```bash
    php artisan storage:link
    ```

7.  **Start the Development Server:**
    ```bash
    php artisan serve
    ```
    The API will be accessible at `http://120.0.1:8000` (or another port if specified).

## API Endpoints

All API endpoints are prefixed with `/api`. The base URL will be `http://127.0.0.1:8000/api`.

| Endpoint                            | Method | Description                                                   | Authentication Required |
| :---------------------------------- | :----- | :------------------------------------------------------------ | :---------------------- |
| `/login`                            | `POST` | Authenticate user and return an API token.                    | No                      |
| `/logout`                           | `POST` | Invalidate the current user's API token.                      | Yes                     |
| `/blogs/index`                            | `GET`  | Get a paginated list of blogs with filters and search.        | Yes                     |
| `/blogs/store`                            | `POST` | Create a new blog post.                                       | Yes                     |
| `/blogs/show/{blog}`                     | `GET`  | Get details of a specific blog.                               | Yes                     |
| `/blogs/update/{blog}`                     | `PATCH`  | Update an existing blog post.                                 | Yes (Owner only)        |
| `/blogs/delete/{blog}`                     | `DELETE` | Delete a blog post.                                           | Yes (Owner only)        |
| `/blogs/liketoggle/{blog}/like-toggle`         | `POST` | Toggle like status for a blog post.                           | Yes                     |

## API Documentation (Postman)

A detailed Postman Collection is provided to test all API endpoints. This collection also includes generated API documentation.

### How to use the Postman Collection

1.  **Import the Collection:**
    * Open Postman.
    * Click on `File -> Import`.
    * Select the provided `Blog Management System.json` Postman collection file.

2.  **Set Environment Variables:**
    * Once imported, open the `Blog Management System` collection.
    * Go to the `Variables` tab.
    * Ensure `baseUrl` is set to `http://127.0.0.1:8000/api` .
    * The `authToken` variable will be automatically set after a successful login.

3.  **Testing the APIs:**
    * **Login First:** Run the `LOGIN-API` request first. This will authenticate you and set the `authToken` variable.
    * All subsequent protected requests (BLOG-related APIs) will use this `authToken` in their `Authorization` header.
    * Explore each request within the collection to understand their parameters and expected responses.

### Viewing API Documentation from Postman

After importing the collection:

1.  In Postman, select the `Blog Management System` collection from the sidebar.
2.  In the right pane, go to the `View Documentation` tab (usually represented by an information icon `i`).
3.  This will display the generated documentation for all your endpoints, including examples, request bodies, and headers.

