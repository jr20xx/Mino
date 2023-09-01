# Mino

[![License](https://img.shields.io/badge/license-Apache-red.svg)](https://github.com/jr20xx/Mino/blob/main/LICENSE)

## Description

A notes taking application made for the web with two different languages in the backend. Why? Because it is funny... or, at least, it was funny for me!

This project uses PHP and Go at the server side, so the first thing you must do to use either one of the versions provided here is to install everything your system may need to run PHP or Go scripts.

To execute PHP scripts, in this case and in almost all cases, you'll need to setup a web server (like [Apache](https://httpd.apache.org/) or [Nginx](https://www.nginx.com/)), the [PHP language binaries](https://www.php.net/downloads.php), [MariaDB](https://mariadb.org/) and, optionaly, [PHPMyAdmin](https://www.phpmyadmin.net/).

In the other hand, to execute Go scripts, you'll need to get the Golang binaries provided by Google and set them up in the operating system you may be using. Instructions to download all that and set things up can be found in the [documentations of Go](https://go.dev/doc/install).

The IDE used to build this entire project was VSCode on Debian 12. The PHP version used to build the application was PHP 8.2.7 and, in the case of Go, the version used was the 1.20.3.

In the files of this project are included all the dependencies needed by the frontend in each case but you can update them by directly downloading newer sources from their official websites; but in case of modification, I can no longer guarantee that the resultant web application will work as expected.

This project makes use of modern and popular frameworks and libraries to build web apps nowadays like [Bootstrap 5](https://getbootstrap.com/), [jQuery](https://jquery.com/), [SweetAlert2](https://sweetalert2.github.io/) and [JS Cookie](https://github.com/js-cookie/js-cookie).

## Table of Contents

- [First things first](#first-things-first)
- [Files structure](#files-structure)
- [Running the application](#running-the-application)
  - [Running the PHP project](#to-execute-the-php-version-of-the-project-do-the-following)
  - [Running the Go project](#to-execute-the-go-version-of-the-project-do-the-following)
- [Database structure](#database-structure)
- [License](#license)

## First things first

Before going any further, check if you already have installed PHP and/or Go. To do that, open a command line and execute the following commands:

```bash
$: php -version
PHP 8.2.7 (cli) (built: Jun  9 2023 19:37:27) (NTS)
Copyright (c) The PHP Group
Zend Engine v4.2.7, Copyright (c) Zend Technologies
    with Zend OPcache v8.2.7, Copyright (c), by Zend Technologies
$: go version  
go version go1.20.3 linux/amd64
```

If the resultant outputs aren't similar to the ones mentioned before, then you should check your installation of PHP and/or Go.

After checking that either PHP or Go is correctly installed, you may proceed to clone this repo by executing:

```bash
git clone https://github.com/jr20xx/Mino
```

## Files structure

This repo contains two folders with two independent projects: one made with PHP in the backend and another one made with Go.

The tree of files in each directory is not the same but they do have some points in common.

Inside of the directory of the corresponding PHP version of this web app, you will find the following files:

```bash
Mino-PHP
├── assets # Folder for "images" and third party resources
│   ├── bootstrap # Bootstrap 5 resources
│   ├── img
│   │   └── rainbow-gradient.png # Image of the right side of the login prompt
│   ├── jquery # jQuery's sources
│   ├── jscookie # JS Cookie's sources
│   └── sweetalert2 # SweetAlert2's sources
├── index.php # Entry point for the web app
└── mino # Application resources folder
    ├── css
    │   └── styles.css # Custom styles for some elements
    ├── db_helper.php # Handler of all the required operations on the database
    ├── js # Scripts to control responsiveness and behavior of each page
    │   ├── login.js
    │   └── notes.js
    ├── login.php # Login page
    ├── notes.php # Notes page
    └── responder.php # Receiver and responder of requests
```

In the other hand, inside of the directory with the Go version, what you'll find is the following:

```bash
Mino-GO
├── assets # Folder for "images" and third party resources
│   ├── bootstrap # Bootstrap 5 resources
│   ├── img
│   │   └── rainbow-gradient.png # Image of the right side of the login prompt
│   ├── jquery # jQuery's sources
│   ├── jscookie # JS Cookie's sources
│   └── sweetalert2 # SweetAlert2's sources
├── go.mod # Go modules configuration file
├── go.sum # File with checksums of external libraries
├── helpers
│   └── db_helper.go # Handler of all the required operations on the database
├── main.go # File where all requests are handled and answered
│           # This is the entry point of the application
└── mino # Application resources folder
    ├── css
    │   └── styles.css # Custom styles for some elements
    ├── js # Scripts to control responsiveness and behavior of each page
    │   ├── login.js
    │   └── notes.js
    ├── login.html # Login page
    └── notes.html # Notes page
```

## Running the application

Now that you are familiarized with the structure of each project, you are ready to run the code and do whatever you want to do with the application. To run each project, I personally recommend VSCode as it simplifies the process of running either PHP or Go codes and it was also the IDE used in the development of both projects.

Next thing to do is to verify that the IDE you'll be using has the required extensions or plugins to execute PHP or Go code. In VSCode, you will need the [PHP](https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode) and the [GoLang](https://marketplace.visualstudio.com/items?itemName=golang.Go) extensions.

### To execute the PHP version of the project, do the following

1. Open the corresponding folder in VSCode and then open the `index.php` file in the editor.
2. Go to the menu bar, open the **Run** menu and select *Run Without Debugging*.
3. Choose the option **Launch built-in server** in the dropdown menu that opens.
4. Wait for a few minutes or seconds until your default browser gets launched and the login page gets opened.
5. To stop the execution of the application, go back to VSCode and just press the stop button in the floating controls you'll see right after started the execution of the file or press Shift + F5.

### To execute the Go version of the project, do the following

1. Open the corresponding folder in VSCode and then open a terminal.
2. Execute `go run main.go`.
3. Wait until you see in the logs a message including: **Starting server now at <http://localhost:8748/>**
4. After that and if no error messages appear, open your browser and navigate to that address.
5. To stop the execution, go back to VSCode, put the focus on the terminal and click Ctrl + C or just kill the terminal session.

## Database structure

If you run any version of the project, maybe you'll notice that you don't have to do anything to create the database. That's because each project includes the scripts to create the database and its tables automatically in the handlers files of all the required operations on the database (the file db_helper.php in the case of the PHP version of the project and the db_helper.go file in the Go version).

The database is named `mino` and it has two tables named `USERS` and `NOTES`. The first table includes the following columns:

- **ID**: to keep a track on the number of users registered;
- **USERNAME**: to save a unique username for each registered user; and
- **PASSWORD**: to store the SHA-512 representation of the password of a registered user.

In the other hand, the `NOTES` table includes the following columns:

- **ID**: to save a unique identifier for the added notes;
- **TITLE**: to store the title of the saved notes;
- **BODY**: to store the body of the saved notes;
- **TIME_STAMP**: to store the timestamp of creation of each saved note; and
- **USER_ID**: to store the identification number of the user who created the note.

## Contribution

Any help will be really appreciated. You can contribute to this repo by creating pull requests or by opening new issues that can help me grow my knowledge. You can also contribute to this repo by adding a star to it and/or sharing the link if you find it helpful in some way. I'd really love that! Have fun and thanks for your visit here!

## License

This project is licensed under the [Apache License](https://github.com/jr20xx/Mino/blob/main/LICENSE).
