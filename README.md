# [academy.AFRICA](https://academy.africa/)

## Introduction

Journalism is evolving so quickly that newsrooms are struggling to keep up.

[academy.AFRICA](https://academy.africa/) seeks to help, by bringing face-to-face training into partner newsrooms across Africa, and by hosting public workshops at monthly [Hacks/Hackers meetups](https://facebook.com/HacksHackersAfrica).

The academy will also offer [online courses](https://courses.academy.africa/) and webinars, designed to teach just one tool or technique at a time, so participants can upgrade their skills at their own pace.

The training will be spearheaded by the continent’s largest digital journalism network, [Code for Africa](https://codeforafrica.org/), with support from [Google News Lab]() and [World Bank](https://www.worldbank.org)'s [Global Media Development Programme](https://blogs.worldbank.org/category/tags/media-development).

Find out more here: https://academy.africa/

## Development

### Documentation
   For detailed documentation on the plugins used, check the [wiki](https://github.com/CodeForAfrica/academyAfrica/wiki)

### Repository Setup Guide

1. **Add SSH Key to WP Engine**

   To get started, add your SSH key to WP Engine. For more information, refer to the [WP Engine SSH Gateway Documentation](https://wpengine.com/support/ssh-gateway/).

2. **Create a Working Directory and Clone the Repository**

   Create a working directory and clone this repository into it.

3. **Download and Restore Backups from WP Engine**

   Download and restore the latest backups from WP Engine using the instructions provided in the [WP Engine Restore Documentation](https://wpengine.com/support/restore/). Unzip the backups into your working directory.

4. **Access the Remote Database Using SSH**

   Use SSH and local port forwarding to access the remote database. Here's an example command:

   ```bash
   ssh -L 3307:127.0.0.1:3306 -i ~/.ssh/wpengine_ed25519 -o IdentitiesOnly=yes sacademyafrica@sacademyafrica.ssh.wpengine.net
   ```

   For more details, refer to the [WP Engine Remote Database Access Guide](https://wpengine.com/support/setting-remote-database-access/).

5. **Update Environment Variables**

   Update the environment variables in the `.env` file using the database credentials from WP Engine. You can find the credentials with the following command once you have SSH access to the remote database:

   ```bash
   grep WPENGINE_SESSION_DB_PASSWORD ./sites/environmentname/_wpeprivate/config.json
   ```

   To access the admin dashboard from localhost, add the following to `wp-config.php` in the `wordpress` directory after the `WP_DEBUG` line:

   ```php
   define( 'WP_SITEURL', 'http://localhost:8080' );
   define( 'WP_HOME', 'http://localhost:8080' );
   ```

6. **Start Containers**

   Run the following command to start the containers using Docker Compose:

   ```bash
   docker-compose up -d
   ```

7. **Access the Site**

   You can now access the site at [http://localhost:8080](http://localhost:8080).
