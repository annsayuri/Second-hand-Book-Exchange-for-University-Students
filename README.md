# 📚 BookBridge Sri Lanka

A second-hand book exchange platform for university students in Sri Lanka.

## 🌟 About The Project

BookBridge Sri Lanka is a web-based platform that connects university 
students who want to sell or exchange their used textbooks with students 
who need them — at affordable prices.

### 🎯 Problem It Solves
University textbooks in Sri Lanka are expensive (LKR 1,500 - 8,000+).
Students currently use disorganized WhatsApp groups to buy/sell books.
BookBridge provides a dedicated, trusted marketplace solution!

## ✨ Features

- 👤 User Registration & Login
- 📚 Post Books for Sale
- 🔍 Search & Filter Books
- 💬 Messaging System
- ⭐ Seller Reviews & Ratings
- 🔖 Wishlist
- 🛡️ Admin Dashboard
- 📱 Mobile Responsive

## 🛠️ Built With

- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend:** PHP 8.x
- **Database:** MySQL 8.x
- **Tools:** XAMPP/WAMP, VS Code, Git

## 🗄️ Database Tables

- `users` - Student accounts
- `books` - Book listings
- `categories` - Book categories
- `messages` - User messages
- `reviews` - Seller reviews
- `wishlist` - Student wishlists

## 🚀 How to Run Locally

1. Clone the repository
```bash
git clone https://github.com/annsayuri/Second-hand-Book-Exchange-for-University-Students.git bookbridge
```

2. Move to WAMP/XAMPP www folder
```bash
C:\wamp64\www\bookbridge
```

3. Import database
- Open phpMyAdmin or MySQL console
- Create database `bookbridge_db`
- Import `database/bookbridge_db.sql`

4. Configure database connection
- Open `includes/db.php`
- Update username and password

5. Run the project
```
http://localhost/bookbridge/
```

## 📁 Project Structure
```
bookbridge/
├── admin/          → Admin panel
├── assets/         → CSS, JS, Images
├── includes/       → Reusable PHP files
├── uploads/        → Book cover images
├── database/       → SQL export file
├── index.php       → Homepage
├── register.php    → Registration
├── login.php       → Login
├── listings.php    → Browse books
├── post-book.php   → Post a book
├── book-detail.php → Book details
├── profile.php     → User profile
├── messages.php    → Messaging
├── wishlist.php    → Wishlist
├── review.php      → Reviews
└── my-books.php    → My listings
```

## 👩‍💻 Developer

- **Name:** Ann Sayuri
- **Course:** Web Programming
- **Year:** 2nd Year
- **University:** BCI Campus, Negombo, Sri Lanka.

## 📄 License

This project is licensed under the MIT License.
