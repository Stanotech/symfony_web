This application is a recruitment task to Macopedia on PHP/Symfony backend developer.  
Task requirements are in task_requirements.odt file 
<br>

For installation just type:

    git clone https://github.com/Stanotech/symfony_web.git
    cd app
    ddev start
    php bin/console doctrine:migrations:migrate

<br>
Description:
<br>

This project is a PHP 8.2-based API for managing users, roles, and posts, built using Symfony, Doctrine ORM, and API Platform. The application supports authentication with JWT and enforces role-based access control.
<br>
Technologies Used:

    PHP 8.2
    Symfony Framework
    Doctrine ORM
    API Platform
    DDEV for local development
    PostgreSQL
    LexikJWTAuthenticationBundle for authentication

<br>
Features:  


-User Management  
-Create, update, retrieve, and delete users  
-Secure password storage using encryption  
-Role-based access control with the UserRole entity  
-JWT authentication for secure login  
-Post Management  
-Users can create, update, and delete their own posts  
-Admin users can manage all posts  
-Search and sort posts by title, content, author name, or creation date  
-Role Management  
-Create, update, and delete user roles  
-Restrict API access based on role permissions  
-Provide a subresource endpoint to retrieve users assigned to a role  
-API Endpoints  
-User Endpoints  
-GET /users - List users  
-GET /users/{id} - Get user details  
-POST /users - Create a user  
-PUT /users/{id} - Update user  
-PATCH /users/{id} - Partially update user  
-DELETE /users/{id} - Delete user  
-User Role Endpoints  
-GET /user_roles - List user roles  
-GET /user_roles/{id} - Get role details  
-POST /user_roles - Create a role  
-PUT /user_roles/{id} - Update role  
-PATCH /user_roles/{id} - Partially update role  
-DELETE /user_roles/{id} - Delete role  
-GET /user_roles/{id}/users - Get users assigned to a role  
-Post Endpoints  
-GET /posts - List posts  
-GET /posts/{id} - Get post details  
-POST /posts - Create a post  
-PUT /posts/{id} - Update post  
-PATCH /posts/{id} - Partially update post  
-DELETE /posts/{id} - Delete post  
<br>

Authentication & Security

-Users must authenticate using JWT (/login endpoint)  
-Unauthenticated users can only access public endpoints  
-Users can only modify their own data  
-Admins can manage all users, roles, and posts  
<br><br>
Contribution

Feel free to open issues and submit pull requests.
<br><br>
License

This project is licensed under the MIT License.