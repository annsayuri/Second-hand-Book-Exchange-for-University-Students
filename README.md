# Second-hand-Book-Exchange-for-University-Students
This is my 2nd year final semester project

bookbridge/
│
├── index.php
├── listings.php
├── book-detail.php
├── register.php
├── post-book.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── db.php
│
├── uploads/
│   └── (book images)
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
└── database tables
    ├── users
    ├── books
    └── categories


## How Your Page Actually Runs

When you visits:

http://localhost/bookbridge/index.php

The execution happens top → bottom.

# Step 1

require_once 'includes/header.php';

This loads:

- HTML <head>
- CSS
- navbar

# Example:

<html>
<head>
<title>BookBridge</title>
<link rel="stylesheet" href="bootstrap.css">
</head>
<body>
<nav>...</nav>

# Step 2

require_once 'includes/db.php';

This creates the database connection.

Example inside db.php:

$pdo = new PDO(
"mysql:host=localhost;dbname=bookbridge",
"root",
""
);


  Now the Database Query Runs

Your code:

$stmt = $pdo->query("SELECT b.*, c.name as category_name 
FROM books b 
LEFT JOIN categories c ON b.category_id = c.category_id 
ORDER BY b.created_at DESC 
LIMIT 6");

