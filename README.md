# 📚 BookBridge Sri Lanka

A second-hand book exchange platform for university students in Sri Lanka.

## 🌟 About The Project

BookBridge Sri Lanka is a web-based platform that connects university 
students who want to sell or exchange their used textbooks & reference books with students 
who need them — at affordable prices.

### 🎯 Problem It Solves
University reference books in Sri Lanka are expensive (LKR 1,500 - 8,000+).
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
│
├── admin/
│   ├── dashboard.php          → Admin statistics and overview
│   ├── manage-users.php       → View, delete and manage user roles
│   └── manage-books.php       → View, edit, delete all book listings
│
├── assets/
│   ├── css/style.css          → Custom styles and Bootstrap overrides
│   ├── js/main.js             → Auto-hide alerts and UI interactions
│   └── images/no-book.png     → Default placeholder image
│
├── database/
│   └── bookbridge_db.sql      → Full MySQL database export
│
├── includes/
│   ├── auth_check.php         → Reusable login protection
│   ├── db.php                 → MySQL PDO database connection
│   ├── header.php             → Navigation bar and HTML head
│   └── footer.php             → Footer and Bootstrap JS
│
├── uploads/                   → Book cover images stored here
│
├── index.php                  → Homepage
├── register.php               → Student registration
├── login.php                  → Secure login
├── logout.php                 → Session destroy
├── listings.php               → Browse and filter books
├── book-detail.php            → Full book details
├── post-book.php              → Post a new book
├── edit-book.php              → Edit book listing
├── my-books.php               → Manage personal listings
├── profile.php                → User profile
├── messages.php               → In-platform messaging
├── wishlist.php               → Saved wanted books
├── review.php                 → Star ratings and reviews
├── 404.php                    → Custom error page
├── .gitignore                 → Git ignore rules
├── Concepts.txt               → PHP concepts reference
├── LICENSE                    → MIT License
└── README.md                  → Project documentation
```

## 👩‍💻 Developer

- **Name:** Ann Sayuri S. kotikawaththa
- **E-mail:** ```annsayu12@gmail.com```
- **Course:** Web Programming
- **Year:** 2nd Year 2nd Semester
- **University:** BCI Campus, Negombo, Sri Lanka.

## 📄 License

This project is licensed under the MIT License.
