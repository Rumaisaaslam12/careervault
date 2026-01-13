# CareerVault

CareerVault is a personal career diary and portfolio system that helps you track internships, courses, conferences, skills, and certificates. It also generates a professional PDF CV instantly.

## 🚀 Deploy to Railway

This project is ready to be deployed on [Railway](https://railway.app/).

### Prerequisites

1.  A [Railway](https://railway.app/) account.
2.  [GitHub CLI](https://cli.github.com/) or Git installed (if deploying from CLI).

### Deployment Steps

1.  **Fork/Push to GitHub**: Push this repository to your GitHub account.
2.  **Create New Project on Railway**:
    *   Go to your Railway Dashboard.
    *   Click "New Project" -> "Deploy from GitHub repo".
    *   Select your `careervault` repository.
3.  **Add Database**:
    *   In your Railway project view, right-click (or click "New") -> "Database" -> "MySQL".
    *   This will add a MySQL service to your project.
4.  **Connect App to Database**:
    *   Railway usually automatically injects `MYSQL_URL` and other variables.
    *   However, this app uses standard environment variables. You need to map them in your App Service "Variables" tab:
        *   `DB_HOST`: `${{MySQL.MYSQLHOST}}`
        *   `DB_PORT`: `${{MySQL.MYSQLPORT}}`
        *   `DB_NAME`: `${{MySQL.MYSQLDATABASE}}`
        *   `DB_USER`: `${{MySQL.MYSQLUSER}}`
        *   `DB_PASS`: `${{MySQL.MYSQLPASSWORD}}`
5.  **Import Database Schema**:
    *   Copy the content of `database.sql`.
    *   Go to the MySQL service in Railway -> "Data" tab (or use an external tool like TablePlus/DBeaver connected to your Railway DB).
    *   Run the SQL query to create the tables.
6.  **Visit your App**:
    *   Railway will generate a domain for your service (e.g., `careervault-production.up.railway.app`).
    *   Open it and log in!

### ⚠️ Important Note on File Uploads

This application uses **local file storage** for certificates (`uploads/`). On platforms like Railway, the filesystem is **ephemeral**, meaning uploaded files will be **deleted** whenever the application redeploys or restarts.

**For Production Use:**
To persist files, you should modify the application to use an object storage service like AWS S3, Google Cloud Storage, or Cloudinary.

## Local Development

1.  Start a local server:
    ```bash
    php -S localhost:8000
    ```
2.  Open `http://localhost:8000` in your browser.
3.  Ensure your local MySQL server is running and `config/db.php` can connect (defaults to localhost/root/empty password).

## Features

*   **Activity Tracking**: Internships, Courses, Conferences, Workshops.
*   **Skill Management**: Add and rate skills.
*   **Certificate Storage**: Upload and manage certificates.
*   **CV Generator**: Download a PDF CV generated from your data using FPDF.
*   **Dashboard**: Overview of your career progress.
