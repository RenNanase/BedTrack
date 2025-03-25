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
git clone https://github.com/RenNanase/bedtrack.git
cd bedtrack
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


