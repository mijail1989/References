
#  🚀 References - Create Your Online Portfolio with Symfony 🚀

References is an ongoing REST API project aimed at providing users with the ability to create and manage their own online portfolios. Built with Symfony, a powerful PHP framework, this project offers a feature-rich platform for showcasing your skills, projects, and achievements in a personalized portfolio.

🔥 Key Features:
- User Registration and Authentication: Easily sign up and log in to start building your portfolio.
- Portfolio Creation: Create and customize your portfolio with various sections like About Me, Projects, Skills, and Contact.
- Project Showcase: Showcase your projects with detailed descriptions, images, and links.
- Skill Proficiency: Highlight your skills and proficiency levels for different technologies and tools.
- Contact Form: Enable a contact form to receive messages from potential clients or collaborators.
- Responsive Design: Ensure your portfolio looks great on various devices, from desktops to mobile devices.

🛠️ Work in Progress:
References is actively evolving, and we are continuously adding new features and improvements. Our development team is committed to creating a seamless experience for our users and exploring innovative ways to enhance the platform.

📝 Note:
As the project is under active development, some features might be subject to change, and we appreciate your understanding and feedback during this phase.

Stay tuned for more updates as we continue to shape Reference into an even more powerful and user-friendly platform for online portfolios!

# 🐳 Docker + PHP 8.2 + MySQL + Nginx + Symfony 6.2 Boilerplate

## Description

This is a complete stack for running Symfony 6.2 into Docker containers using docker-compose tool.

It is composed by 4 containers:

- `nginx`, acting as the webserver.
- `php`, the PHP-FPM container with the 8.2 version of PHP.
- `db` which is the MySQL database container with a **MySQL 8.0** image.

## Installation

1. 😀 Clone this repo.

2. If you are working with Docker Desktop for Mac, ensure **you have enabled `VirtioFS` for your sharing implementation**. `VirtioFS` brings improved I/O performance for operations on bind mounts. Enabling VirtioFS will automatically enable Virtualization framework.

3. Create the file `./.docker/.env.nginx.local` using `./.docker/.env.nginx` as template. The value of the variable `NGINX_BACKEND_DOMAIN` is the `server_name` used in NGINX.

4. Go inside folder `./docker` and run `docker compose up -d` to start containers.

5. You should work inside the `php` container. This project is configured to work with [Remote Container](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) extension for Visual Studio Code, so you could run `Reopen in container` command after open the project.

6. Inside the `php` container, run `composer install` to install dependencies from `/var/www/symfony` folder.

7. Use the following value for the DATABASE_URL environment variable:

```
DATABASE_URL=mysql://app_user:helloworld@db:3306/app_db?serverVersion=8.0.33
```

You could change the name, user and password of the database in the `env` file at the root of the project.

## To learn more

I have recorded a Youtube session explaining the different parts of this project. You could see it here:

[Boilerplate para Symfony basado en Docker, NGINX y PHP8](https://youtu.be/A82-hry3Zvw)
