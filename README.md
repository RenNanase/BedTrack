# BedTrack System

## 📌 Overview
BedTrack is a **hospital bed management system** designed to streamline bed allocation, tracking, and monitoring. It helps healthcare facilities **optimize patient flow**, reduce wait times, and ensure efficient utilization of resources.

## 🚀 Features
- 🏥 **Real-Time Bed Tracking** – Monitor bed availability and occupancy status.
- 🔄 **Patient Bed Assignment** – Assign and transfer patients seamlessly.
- 🧹 **Automated Bed Status Updates** – Track bed readiness after cleaning or maintenance.
- 📊 **Reports & Analytics** – Generate insights on occupancy rates and bed turnover.
- 🔐 **User Roles & Authentication** – Secure access for nurses, admins, and hospital staff.

## 🎨 Technology Stack
- **Framework:** Laravel 12
- **Frontend:** Blade, Tailwind CSS
- **Database:** MySQL
- **Version Control:** Git & GitHub

## 🛠️ Installation Guide
### **1️⃣ Clone the Repository**
```bash
git clone https://github.com/RenNanase/BedTrack.git
cd BedTrack
```
### **2️⃣ Install Dependencies**
```bash
composer install
npm install && npm run dev
```
### **3️⃣ Configure Environment**
Copy the `.env.example` file and set up your database:
```bash
cp .env.example .env
php artisan key:generate
```
Set up the database credentials in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bedtrack
DB_USERNAME=root
DB_PASSWORD=yourpassword
```
### **4️⃣ Run Migrations**
```bash
php artisan migrate
```
### **5️⃣ Start the Application**
```bash
php artisan serve
```
Your system will be available at `http://127.0.0.1:8000`

## Automatic Housekeeping Status Updates

The system includes an automatic function that changes bed status from "Housekeeping" to "Available" after a 2-hour period. This feature ensures proper workflow for bed cleaning and preparation between patients.

### Setting Up the Scheduler

For this automatic feature to work, you need to set up Laravel's scheduler to run regularly. Add this Cron entry to your server:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Replace `/path-to-your-project` with the actual path to the BedTrack application.

#### Windows (For Development)

On Windows, you can use the Task Scheduler to run the command:

```
cd C:\path\to\your\project && php artisan schedule:run
```

#### Alternative Manual Testing

You can manually trigger the auto-update process by running:

```
php artisan beds:update-status
```

## Bed Status Workflow

1. A bed with a patient is marked as "Discharged" when the patient leaves
2. The system automatically changes the status to "Housekeeping" 
3. After 2 hours (or when manually changed), the status is set to "Available"
4. The bed is now ready for a new patient

## Features

- Real-time bed status tracking
- Patient admission and discharge management
- Automatic housekeeping workflow
- Activity logging
- Dashboard with key metrics
- Ward-based organization

## Requirements

- PHP 8.1+
- MySQL 5.7+
- Composer
- Node.js and NPM (for frontend assets)


