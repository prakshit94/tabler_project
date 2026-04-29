# Village & Inventory ERP (Modernized)

A high-performance, enterprise-grade ERP system built with **Laravel 11** and the **Tabler UI** design system. This platform is designed to manage large-scale village data, agricultural supply chains, complex warehouse logistics, and double-entry accounting.

---

## 🌟 Key Modules & Features

### 1. Village & Geographic Master
- **Comprehensive Village Database:** Track Village Name, Pincode, Post Office, Taluka, District, and State.
- **AJAX Search & Auto-fill:** Intelligent search that auto-fills geographic details in registration and checkout forms.
- **Bulk Operations:** Multi-select delete and restore functionality.

### 2. CRM & Party Management
- **Unified Profiles:** Manage Farmers and Vendors in a single, data-dense interface.
- **Farmer Portfolio:** Track Land Area, Irrigation Types, and Crops Portfolio.
- **Address Book:** Support for multiple Billing and Shipping addresses with "Set as Default" logic.

### 3. WMS (Warehouse Management System)
- **Real-time Dashboard:** Monitor pending picks, packing queues, and low-stock alerts.
- **Picking & Packing:** Digital pick lists and multi-package support for shipping.
- **Backorder Management:** Automatically track and fulfill items that are out of stock during order confirmation.
- **Shipment Tracking:** Generate Delivery Challans and track shipping events (Dispatched, In Transit, Delivered).

### 4. Inventory & Products
- **Rich Product Catalog:** Support for SKU tracking, categories, brands, and multiple images.
- **Stock Control:** Precise tracking of On-Hand, Reserved, Committed, and In-Transit quantities.
- **Stock Transfers:** Move inventory between warehouses with full audit trails.

### 5. Sales & Accounting
- **Dynamic Cart System:** Real-time quantity updates and tax calculations.
- **Double-Entry Accounting:** Automated ledger postings for every transaction.
- **Financial Reports:** Trial Balance and individual Ledger Statements.
- **Tally Sync:** Built-in XML integration for syncing data with Tally ERP 9.

---

## 📖 How to Use

### Managing Master Data
- Navigate to **System Setup > Agriculture & Master Data** in the sidebar.
- Use **Villages** to upload or create geographic records.
- Use **Crops, Land Units, and Irrigation Types** for agricultural profiling.

### Managing Farmers/Vendors
- Go to **Sales & CRM > Farmer & Vendor Directory**.
- Use the **Quick Search (Mobile)** to find existing members.
- Click **"View Profile"** to access the 360-degree view (Orders, Catalog, Settings).

### Fulfilling Orders
- Orders created by customers appear in the **Warehouse Operations** hub.
- Move orders from `Pending` → `Picking Tasks` → `Packing Queue` → `Shipments`.
- Once shipped, generate a **Tax Invoice** from the **Finance & Accounting** section.

---

## 🧪 Step-by-Step End-to-End Testing (Manual)

Follow these 10 steps to verify the entire system workflow:

1.  **Preparation:** Navigate to **Inventory** and ensure at least one product has stock > 10.
2.  **Customer Context:** Go to **Farmers**, search for a customer, and click **View Profile**.
3.  **Shopping:** Open the **Product Catalog** tab. Add 5 units of a product to the cart.
4.  **Checkout Fix Test:** 
    - Switch to the **Review Order** tab.
    - Click **"New Address"**. Add a new address and Save.
    - **Verify:** The system must stay on the **Review Order** tab (it should not redirect to the first Profile tab).
5.  **Order Placement:** Select a warehouse and click **Place Order Now**.
6.  **WMS Pick List:** 
    - Go to `Inventory & Logistics > Warehouse Operations > Picking Tasks`.
    - Click **Generate Pick List** for your new order.
    - Click **Start Picking**, enter quantities, and Finish.
7.  **WMS Packing:**
    - Go to `Inventory & Logistics > Warehouse Operations > Packing Queue`.
    - Create a Package, enter weight, and click **Seal Package**.
8.  **Shipping:**
    - Go to `Inventory & Logistics > Warehouse Operations > Shipments Tracking`.
    - Click **Create Shipment** for your order to generate the **Delivery Challan**.
9.  **Accounting:**
    - Go to `Finance & Accounting > Invoices`. Generate the Tax Invoice.
    - Go to `Finance & Accounting > Payments`. Record a partial payment of ₹1,000.
    - **Verify:** The invoice status should move to `partial`.
10. **Returns:**
    - Go to `Sales & CRM > Returns`.
    - Select your order and return 1 unit.
    - **Verify:** Check **Inventory Stock Levels**; the "On Hand" count must increase by 1.

---

## 🛠️ Tech Stack & Requirements

- **Backend:** PHP 8.2+ | Laravel 11
- **UI:** Tabler (Vanilla JS / Bootstrap 5)
- **Database:** MySQL 8.0
- **Logistics:** Spatie Activity Log (for audit trails)

## ⚙️ Quick Setup

```bash
# Install dependencies
composer install
npm install && npm run dev

# Setup environment
cp .env.example .env
php artisan key:generate

# Database Setup
php artisan migrate --seed
php artisan db:seed --class=AgricultureMasterDataSeeder
php artisan db:seed --class=VillageSeeder
```

---
Developed with ❤️ by the ERP Modernization Team.
