# CMC Organik Official Website

This repository contains the source code for the official corporate website of **CMC Organik LTD ŞTİ**, a company specializing in sustainable agriculture and organic solutions. The initial development phase is complete, and the deployed system is currently fully operational and actively managed.

## 🌐 Live Website

The current, active version of the system can be viewed here:
* **URL:** [https://www.cmcorganik.com](https://www.cmcorganik.com)

## ✨ Key Features

The website is designed as a comprehensive, dynamic platform with a strong focus on internal management and external communication.

* **Admin Panel:** A dedicated, secure backend for content administrators.
    * **Product Management:** Full CRUD (Create, Read, Update, Delete) functionality for managing the company's product catalog.
    * **Content Management:** Ability to update website structure, pages, and dynamic content (e.g., news, gallery, company info).
    * **Communication Hub:** Tools for viewing and responding to messages submitted via the contact forms.
* **Responsive Frontend:** A modern, fully responsive user interface built for easy navigation.
* **Database Driven:** All dynamic content, including products and contact messages, is managed via a MySQL/SQL database.

---

## 📈 Development Status & Optimization Roadmap

The project is currently transitioning from the initial development phase into a dedicated **Performance Improvement and Optimization Phase**. While the core platform is stable and operational, the focus is now on technical enhancements to maximize speed, scalability, and content delivery efficiency.

### Primary Focus: Asset and Transfer Optimization

The immediate technical priority is enhancing content delivery and administrative performance. Specific areas for improvement include:

* **Accelerated Asset Handling:** Implementing advanced techniques for **image compression and lazy loading** (e.g., utilizing next-gen formats or optimizing existing file structures) to significantly minimize client-side load times.
* **Optimized Upload Flow:** Streamlining the asset upload process (photos and files) within the Admin Panel to ensure **faster, more reliable concurrent transfers** and improved administrative efficiency.
* **General Performance Tuning:** Reviewing server-side caching mechanisms and database query efficiency to ensure rapid delivery of both static and dynamic content.

This optimization phase aims to guarantee a faster user experience, especially for global access, and provide a highly responsive management environment for content administrators.

---

## 💻 Technologies Used

The project is primarily built using a PHP/MySQL stack:

* **Backend Logic:** PHP
* **Database:** SQL (MySQL/MariaDB)
* **Frontend:** HTML, CSS, JavaScript

## ⚙️ Installation & Setup (Local Environment)

1.  **Prerequisites:** You need a web server environment (like XAMPP, Laragon, or a LAMP stack) with PHP and MySQL installed.
2.  **Clone the Repository:**
    ```bash
    git clone [https://github.com/Decuayer/cmc-organik-website.git](https://github.com/Decuayer/cmc-organik-website.git)
    ```
3.  **Database Setup:** Create a new database and import the necessary tables using the scripts found in the `sql/` directory.
4.  **Configuration:** Update the database connection credentials within the `config/` directory to match your local setup.
5.  **Run:** Place the project files in your web server's root directory to access the website.

## 🤝 Contact

For maintenance or support requests regarding this project, please contact the developer.
