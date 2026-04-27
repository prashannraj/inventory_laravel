# Inventory Management System with POS Features - Complete User Guide

## 📋 Table of Contents
1. [Introduction & System Overview](#1-introduction-system-overview)
2. [Getting Started](#2-getting-started)
3. [Dashboard Navigation](#3-dashboard-navigation)
4. [Product Management](#4-product-management)
5. [Inventory Control](#5-inventory-control)
6. [Point of Sale (POS) System](#6-point-of-sale-pos-system)
7. [Sales & Invoicing](#7-sales-invoicing)
8. [Purchase Management](#8-purchase-management)
9. [Supplier & Customer Management](#9-supplier-customer-management)
10. [Reporting & Analytics](#10-reporting-analytics)
11. [User Management & Permissions](#11-user-management-permissions)
12. [Company Settings](#12-company-settings)
13. [Payment Gateway Integration](#13-payment-gateway-integration)
14. [Troubleshooting & Support](#14-troubleshooting-support)

---

## 1. Introduction & System Overview

### What is This System?
This is a comprehensive **Inventory Management System with integrated Point of Sale (POS)** features designed for retail businesses, warehouses, and distributors. The system helps you manage your entire inventory lifecycle from procurement to sales.

### Key Features
- **Real-time Inventory Tracking** - Monitor stock levels across multiple stores
- **Point of Sale (POS)** - Fast checkout with barcode scanning
- **Purchase Order Management** - Streamline procurement from suppliers
- **Sales & Invoicing** - Generate professional invoices
- **Multi-store Support** - Manage inventory across locations
- **Reporting Dashboard** - Financial and inventory analytics
- **User Role Management** - Control access with permissions

### System Requirements
- Web browser (Chrome, Firefox, Edge)
- Internet connection
- User account with appropriate permissions

---

## 2. Getting Started

### 2.1 First-Time Login
1. **Access the System**: Open your browser and navigate to the system URL
2. **Login Credentials**: Use the username and password provided by your administrator
3. **Dashboard**: After login, you'll be directed to the main dashboard

### 2.2 Initial Setup Checklist
Complete these steps before starting operations:
- [ ] Set up Company Information
- [ ] Configure Store/Location details
- [ ] Add Product Categories and Brands
- [ ] Set up Tax Rates
- [ ] Add Measurement Units
- [ ] Create Supplier Records
- [ ] Add Initial Product Inventory

### 2.3 Navigation Basics
- **Sidebar Menu**: Access all modules from the left sidebar
- **Top Navigation**: User profile and quick actions
- **Dashboard**: Central hub with key metrics
- **Breadcrumbs**: Track your location within the system

---

## 3. Dashboard Navigation

### 3.1 Understanding the Dashboard
The dashboard provides real-time business insights:

**Key Widgets:**
- **Net Revenue**: Total revenue after deductions
- **Total Sales**: Number of completed sales transactions
- **Active Products**: Products currently in stock
- **Low Stock Alerts**: Products needing replenishment

**Revenue Chart:**
- Visual representation of sales performance
- Filter by date range (Today, Week, Month, Year)
- Toggle between different metrics

**Recent Activities:**
- Latest sales transactions
- Recent stock adjustments
- System notifications

### 3.2 Quick Actions
From the dashboard, you can quickly:
- Start a new sale (POS)
- Add a new product
- Create a purchase order
- View low stock reports

---

## 4. Product Management

### 4.1 Adding a New Product
**Step-by-Step Guide:**
1. Navigate to **Products → Add New Product**
2. Fill in basic information:
   - **Product Name**: Descriptive name (e.g., "Apple iPhone 15 Pro")
   - **SKU**: Unique stock keeping unit (auto-generated or manual)
   - **Barcode**: Scan or enter barcode (ISBN, UPC, EAN)
   - **Category**: Select from existing categories
   - **Brand**: Choose product brand
   - **Unit**: Measurement unit (Piece, Kg, Liter, etc.)

3. Configure pricing:
   - **Cost Price**: Purchase price from supplier
   - **Selling Price**: Retail price to customers
   - **Tax Rate**: Applicable tax percentage
   - **Discount**: Optional percentage discount

4. Set inventory parameters:
   - **Initial Stock**: Starting quantity
   - **Reorder Level**: Minimum stock threshold for alerts
   - **Maximum Stock**: Storage capacity limit
   - **Store/Location**: Where product is stored

5. Add product details:
   - **Description**: Detailed product information
   - **Images**: Upload product photos
   - **Attributes**: Size, color, model variations
   - **Supplier**: Primary supplier information

6. Click **Save Product**

**Example: Adding a Smartphone**
```
Product Name: Samsung Galaxy S24
SKU: SM-GS24-BLK-256
Barcode: 8806094676543
Category: Electronics → Mobile Phones
Brand: Samsung
Unit: Piece
Cost Price: $850.00
Selling Price: $999.00
Tax Rate: 13%
Initial Stock: 25 units
Reorder Level: 5 units
Store: Main Store
```

### 4.2 Managing Product Categories
**Creating Categories:**
1. Go to **Products → Categories**
2. Click **Add New Category**
3. Enter category name and description
4. Optionally set parent category for hierarchy
5. Save

**Category Structure Example:**
```
Electronics
├── Mobile Phones
│   ├── Smartphones
│   └── Feature Phones
├── Laptops
│   ├── Gaming Laptops
│   └── Business Laptops
└── Accessories
    ├── Chargers
    └── Cases
```

### 4.3 Product Attributes & Variations
For products with variations (size, color, etc.):
1. Go to **Products → Attributes**
2. Create attribute (e.g., "Color")
3. Add values (Red, Blue, Green, Black)
4. Assign attributes to products
5. Manage stock for each variation separately

### 4.4 Bulk Product Operations
- **Import Products**: Use CSV import for large catalogs
- **Export Products**: Download product list for offline review
- **Bulk Update**: Edit multiple products simultaneously
- **Bulk Price Adjustment**: Apply percentage/amount changes to selected products

---

## 5. Inventory Control

### 5.1 Stock Level Monitoring
**Viewing Current Stock:**
1. Navigate to **Products → Product List**
2. View stock levels in the "Stock" column
3. Color indicators:
   - **Green**: Adequate stock (> reorder level)
   - **Yellow**: Low stock (approaching reorder level)
   - **Red**: Critical stock (below reorder level)
   - **Gray**: Out of stock

**Stock Details:**
- Click on any product to view:
  - Current quantity across all stores
  - Stock value (cost and selling)
  - Stock movement history
  - Pending purchase orders

### 5.2 Stock Adjustments
**When to adjust stock:**
- Physical inventory count discrepancies
- Damaged or expired goods
- Sample products
- Theft or loss

**Making an Adjustment:**
1. Go to **Inventory → Stock Adjustments → New Adjustment**
2. Select adjustment type:
   - **Addition**: Increase stock (found items, returns)
   - **Reduction**: Decrease stock (damage, samples)
3. Choose reason from predefined list or enter custom reason
4. Select products and enter adjustment quantities
5. Add notes for audit trail
6. Submit adjustment

**Example Adjustment:**
```
Date: April 25, 2026
Type: Reduction
Reason: Damaged during handling
Products: 
  - Coca-Cola 330ml: -12 cans
  - Lays Chips: -5 packets
Notes: Damage discovered during monthly stock check
```

### 5.3 Stock Transfers Between Stores
**Transfer Process:**
1. Go to **Inventory → Stock Transfers**
2. Select source and destination stores
3. Choose products and quantities
4. Assign responsible personnel
5. Generate transfer reference number
6. Confirm receipt at destination

**Transfer Status Tracking:**
- **Pending**: Created but not processed
- **In Transit**: Being transferred
- **Completed**: Received at destination
- **Cancelled**: Transfer cancelled

### 5.4 Inventory Counting (Physical Stocktake)
**Periodic Count Procedure:**
1. **Preparation**:
   - Print inventory count sheets
   - Freeze transactions during count
   - Assign counting teams

2. **Counting**:
   - Scan/enter product barcodes
   - Record actual quantities
   - Note discrepancies

3. **Reconciliation**:
   - Upload count data
   - System compares with book stock
   - Generate variance report
   - Create adjustment entries

4. **Approval**:
   - Manager review and approval
   - System updates stock levels
   - Generate audit report

---

## 6. Point of Sale (POS) System

### 6.1 POS Interface Overview
**Main Components:**
1. **Product Search/Scan Area**: Quick product lookup
2. **Cart/Order Summary**: Current transaction items
3. **Customer Information**: Customer details and history
4. **Payment Panel**: Payment methods and amounts
5. **Quick Actions**: Discounts, holds, returns

### 6.2 Making a Sale (Step-by-Step)
**Step 1: Start New Sale**
- Click **POS** in sidebar or **New Sale** on dashboard
- System generates new transaction number
- POS interface loads

**Step 2: Add Products to Cart**
- **Method A: Barcode Scanning**
  - Connect barcode scanner
  - Scan product barcode
  - Product automatically added to cart

- **Method B: Manual Search**
  - Type product name/SKU in search box
  - Select from suggestions
  - Click "Add" or press Enter

- **Method C: Product Grid**
  - Browse product categories
  - Click product tile to add

**Step 3: Adjust Quantities & Prices**
- Click on cart item to modify:
  - Change quantity (use +/- buttons or type)
  - Apply item-specific discount (% or amount)
  - Update price (manager override may require permission)

**Step 4: Add Customer (Optional)**
- Click "Select Customer"
- Search existing customer or "Add New"
- Customer benefits:
  - Track purchase history
  - Apply loyalty discounts
  - Email receipt

**Step 5: Apply Transaction-wide Discount**
1. Click **Discount** button
2. Choose discount type:
   - **Percentage**: 10% off total
   - **Fixed Amount**: $5 off
   - **Buy X Get Y**: Promotional offers
3. Enter discount value
4. Review updated total

**Step 6: Process Payment**
1. Click **Payment** button
2. Enter amounts for each payment method:
   - **Cash**: Amount tendered
   - **Card**: Credit/Debit card
   - **Mobile Payment**: QR code, digital wallet
   - **Bank Transfer**: Reference number
3. System calculates:
   - Total amount due
   - Amount paid
   - Change to return (if cash overpayment)

**Step 7: Complete Sale**
1. Review final invoice
2. Click **Complete Sale**
3. Choose receipt options:
   - **Print Receipt**: Thermal printer
   - **Email Receipt**: Send to customer email
   - **SMS Receipt**: Send to customer phone
4. Transaction saved, stock updated

### 6.3 POS Shortcuts & Tips
**Keyboard Shortcuts:**
- `F1`: New sale
- `F2`: Search product
- `F3`: Focus on quantity
- `F4`: Apply discount
- `F5`: Payment screen
- `F6`: Print receipt
- `F7`: Hold transaction
- `F8`: Recall held transaction
- `F9`: Void item
- `F10`: Cancel transaction

**Quick Actions:**
- **Hold Sale**: Save incomplete transaction for later
- **Split Payment**: Multiple payment methods
- **Price Override**: Manager permission required
- **Return Item**: Process returns from same screen

### 6.4 Handling Returns & Refunds
**Return Process:**
1. From POS, click **Returns** button
2. Enter original sale number or scan receipt
3. Select items to return
4. Choose return reason:
   - Defective
   - Wrong item
   - Customer dissatisfaction
5. Select refund method:
   - Cash refund
   - Store credit
   - Exchange for other product
6. Process refund, update inventory

### 6.5 Managing Open Drawer
**Cash Management:**
- **Opening Balance**: Enter starting cash at shift start
- **Cash In/Out**: Record non-sale cash movements
- **Closing Count**: Count cash at shift end
- **Cash Difference**: System compares expected vs actual
- **Drop Safe**: Record cash deposits to safe

**Shift Report:**
- Total sales by payment method
- Number of transactions
- Voided transactions
- Discounts given
- Cash drawer summary

---

## 7. Sales & Invoicing

### 7.1 Sales Management
**Viewing Sales History:**
1. Navigate to **Sales → Sales List**
2. Filter by:
   - Date range
   - Customer
   - Payment status
   - Store location
3. View sales details:
   - Items sold
   - Quantities and prices
   - Discounts applied
   - Payment information

**Sales Status Types:**
- **Completed**: Fully paid and delivered
- **Pending**: Created but not paid
- **Partial**: Partially paid
- **Cancelled**: Sale cancelled
- **Refunded**: Fully or partially refunded

### 7.2 Creating Manual Sales (Non-POS)
**For phone/email orders:**
1. Go to **Sales → New Sale**
2. Select customer (or create new)
3. Add products manually
4. Set delivery/shipping details
5. Generate proforma invoice
6. Mark as paid when payment received

### 7.3 Invoicing System
**Invoice Components:**
1. **Header**:
   - Invoice number (auto-generated)
   - Date and time
   - Customer details
   - Salesperson

2. **Body**:
   - Itemized product list
   - Quantities, rates, amounts
   - Taxes applied
   - Discounts

3. **Footer**:
   - Subtotal
   - Tax total
   - Discount total
   - Grand total
   - Payment terms
   - Due date

**Invoice Customization:**
- Add company logo
- Custom footer text
- Terms and conditions
- Payment instructions
- Tax registration numbers

**Sending Invoices:**
- **Print**: Direct to printer
- **PDF Download**: Save for records
- **Email**: Send to customer
- **Share Link**: Generate shareable URL

### 7.4 Payment Collection
**Recording Payments:**
1. Open sale from sales list
2. Click **Receive Payment**
3. Enter payment details:
   - Amount received
   - Payment method
   - Reference number
   - Date
4. Update payment status

**Partial Payments:**
- System tracks remaining balance
- Send payment reminders
- Apply late fees if configured

**Payment Methods Supported:**
- Cash
- Credit/Debit Card
- Bank Transfer
- Mobile Payment (eSewa, Khalti, etc.)
- Check
- Store Credit
- Digital Wallet

---

## 8. Purchase Management

### 8.1 Purchase Order Process
**Creating a Purchase Order:**
1. Go to **Purchases → New Purchase**
2. Select supplier from list
3. Add products needing replenishment:
   - Search by name or SKU
   - Enter quantity to order
   - Set expected cost (if different from current)
4. Set delivery details:
   - Expected delivery date
   - Shipping method
   - Delivery address
5. Add notes to supplier
6. Generate PO number
7. Send to supplier (email/print)

**Purchase Order Status:**
- **Draft**: Created but not sent
- **Sent**: Forwarded to supplier
- **Confirmed**: Supplier acknowledged
- **Partial**: Partially received
- **Completed**: Fully received
- **Cancelled**: Order cancelled

### 8.2 Receiving Goods
**When shipment arrives:**
1. Go to **Purchases → Receive Goods**
2. Enter PO number or select from list
3. Check received items against PO:
   - Verify quantities
   - Inspect quality
   - Note discrepancies
4. Update received quantities
5. Enter actual costs (if different)
6. Update stock levels automatically

**Handling Discrepancies:**
- **Short Shipment**: Fewer items received
- **Over Shipment**: Extra items received
- **Damaged Goods**: Unusable items
- **Wrong Items**: Different products received

**Partial Receiving:**
- Receive items in multiple shipments
- System tracks remaining expected items
- Update PO status accordingly

### 8.3 Purchase Returns
**Returning to Supplier:**
1. Select purchase from history
2. Click **Return Items**
3. Select items and quantities to return
4. Specify return reason
5. Generate return authorization
6. Update inventory and accounts payable

### 8.4 Supplier Payments
**Recording Payments to Suppliers:**
1. Go to **Purchases → Supplier Payments**
2. Select supplier
3. View outstanding invoices
4. Enter payment details:
   - Amount paid
   - Payment method
   - Date
   - Reference number
5. Update account balance

---

## 9. Supplier & Customer Management

### 9.1 Supplier Management
**Adding a New Supplier:**
1. Go to **Suppliers → Add New**
2. Enter company details:
   - Supplier name
   - Contact person
   - Phone, email, address
   - Tax identification number
3. Set payment terms:
   - Credit period (days)
   - Discount for early payment
   - Preferred payment method
4. Add bank details (for transfers)
5. Save supplier record

**Supplier Performance Tracking:**
- On-time delivery rate
- Product quality ratings
- Price competitiveness
- Payment term compliance

### 9.2 Customer Management
**Creating Customer Profiles:**
1. Go to **Customers → Add New**
2. Enter customer information:
   - Personal/Company name
   - Contact details
   - Billing and shipping addresses
   - Tax status (GST/VAT number)
3. Set credit limits:
   - Maximum credit amount
   - Payment terms
   - Credit period
4. Save customer record

**Customer Categories:**
- **Walk-in**: One-time customers
- **Retail**: Regular individual customers
- **Wholesale**: Business customers with bulk orders
- **Corporate**: Large accounts with contracts

**Customer Loyalty Features:**
- Purchase history tracking
- Loyalty points system
- Special discount tiers
- Birthday/anniversary offers

### 9.3 Customer Communication
**Sending Notifications:**
1. **Order Updates**: Order confirmation, shipping updates
2. **Payment Reminders**: For overdue invoices
3. **Promotional Offers**: Special discounts, new arrivals
4. **Birthday Greetings**: Automated birthday messages

**Communication Channels:**
- Email
- SMS
- WhatsApp (if integrated)
- In-app notifications

---

## 10. Reporting & Analytics

### 10.1 Sales Reports
**Generating Sales Reports:**
1. Navigate to **Reports → Sales Reports**
2. Select date range (Today, Week, Month, Custom)
3. Choose report type:
   - **Summary Report**: Total sales, discounts, taxes
   - **Detailed Report**: Itemized sales by product
   - **Customer Report**: Sales by customer
   - **Payment Method Report**: Sales by payment type

**Key Sales Metrics:**
- **Gross Sales**: Total sales before discounts
- **Net Sales**: Sales after discounts
- **Average Transaction Value**: Revenue per sale
- **Items per Transaction**: Average products per sale
- **Conversion Rate**: Sales vs inquiries

### 10.2 Inventory Reports
**Stock Valuation Report:**
1. Go to **Reports → Inventory Reports**
2. View:
   - Total inventory value (cost and retail)
   - Stock turnover rate
   - Aging inventory (slow-moving items)
   - Stock-out frequency

**Low Stock Report:**
- Products below reorder level
- Suggested reorder quantities
- Supplier information for reordering
- Estimated time to stock-out

**Dead Stock Report:**
- Items with no sales in specified period
- Inventory aging (90+ days without movement)
- Recommendations: Discount, bundle, or return

### 10.3 Purchase Reports
**Supplier Performance Report:**
- Total purchases by supplier
- Average delivery time
- Price comparison across suppliers
- Quality ratings

**Purchase Analysis:**
- Monthly procurement costs
- Category-wise spending
- Seasonal purchase patterns
- Bulk purchase discounts achieved

### 10.4 Financial Reports
**Profit & Loss Statement:**
- Revenue from sales
- Cost of goods sold
- Gross profit margin
- Operating expenses
- Net profit

**Cash Flow Report:**
- Cash inflows (sales, payments)
- Cash outflows (purchases, expenses)
- Opening and closing balances
- Cash position forecast

### 10.5 Exporting Reports
**Export Formats:**
- **PDF**: For printing and sharing
- **Excel**: For further analysis
- **CSV**: For data import to other systems
- **Print**: Direct printing

**Scheduled Reports:**
- Daily sales summary (email at EOD)
- Weekly inventory report
- Monthly financial statements
- Custom schedule as needed

---

## 11. User Management & Permissions

### 11.1 User Roles
**Predefined Roles:**
1. **Administrator**: Full system access
2. **Manager**: All operational functions, limited settings
3. **Cashier**: POS sales, basic product lookup
4. **Inventory Clerk**: Stock management, receiving
5. **Sales Representative**: Sales, customer management
6. **View Only**: Read-only access

### 11.2 Creating Users
**Step-by-Step:**
1. Go to **Users → Add New User**
2. Enter personal details:
   - Full name
   - Email address
   - Phone number
3. Set login credentials:
   - Username (or use email)
   - Password (or send invitation)
4. Assign role and permissions
5. Set store/location access
6. Save and notify user

### 11.3 Permission Management
**Granular Permissions:**
- **Product Management**: Add/edit/delete products
- **Inventory Control**: Stock adjustments, transfers
- **Sales Processing**: POS, discounts, returns
- **Purchase Management**: Create POs, receive goods
- **Financial**: View reports, export data
- **Settings**: Company, tax, store configuration

**Best Practices:**
- Principle of least privilege
- Regular permission reviews
- Role-based access control
- Audit trail of permission changes

### 11.4 User Activity Monitoring
**Track User Actions:**
- Login/logout times
- Sales transactions
- Stock adjustments
- System configuration changes
- Report generation

**Security Features:**
- Password complexity requirements
- Session timeout
- Failed login lockout
- Two-factor authentication (if enabled)

---

## 12. Company Settings

### 12.1 Basic Company Information
**Setting Up:**
1. Go to **Settings → Company Information**
2. Enter:
   - Company name and legal name
   - Contact information (phone, email, address)
   - Tax registration numbers
   - Business registration details
3. Upload company logo
4. Set default currency and language
5. Configure date and time formats

### 12.2 Store/Location Management
**Adding Stores:**
1. Go to **Settings → Stores → Add New**
2. Enter store details:
   - Store name and code
   - Physical address
   - Contact person
   - Opening hours
3. Set inventory settings:
   - Default warehouse for store
   - Reorder level preferences
   - Stock transfer rules

**Multi-store Configuration:**
- Centralized inventory view
- Store-specific pricing
- Inter-store transfers
- Consolidated reporting

### 12.3 Tax Configuration
**Setting Up Tax Rates:**
1. Go to **Settings → Tax Rates**
2. Add tax types:
   - **GST/VAT**: Goods and Services Tax
   - **Sales Tax**: Local sales tax
   - **Service Tax**: For services
3. Configure rates:
   - Percentage rate
   - Effective dates
   - Applicable products/categories
4. Set tax calculation method:
   - Inclusive (tax included in price)
   - Exclusive (tax added at checkout)

### 12.4 Payment Method Setup
**Configuring Payment Options:**
1. Go to **Settings → Payment Methods**
2. Enable/disable methods:
   - Cash
   - Credit/Debit Card
   - Bank Transfer
   - Mobile Payment
   - Digital Wallets
3. Configure payment gateways (if online)
4. Set default payment method

### 12.5 Receipt & Invoice Customization
**Receipt Template:**
1. Go to **Settings → Receipt Templates**
2. Customize:
   - Header information
   - Logo placement
   - Item columns
   - Footer messages
   - Terms and conditions
3. Preview and test print
4. Set as default template

**Invoice Numbering:**
- Prefix/Suffix configuration
- Sequential numbering
- Reset rules (yearly, monthly)
- Custom formats

### 12.6 System Preferences
**General Settings:**
- Default language
- Date and time format
- Number formatting
- Auto-logout duration
- Notification preferences

**Inventory Settings:**
- Low stock alert threshold
- Auto-reorder suggestions
- Stock valuation method (FIFO, Average)
- Barcode generation rules

**Sales Settings:**
- Default discount limits
- Maximum price override
- Receipt printing options
- Invoice terms

---

## 13. Payment Gateway Integration

The system supports integration with popular Nepali payment gateways like eSewa and Khalti for digital payments. This section covers configuration and usage of these payment gateways.

### 13.1 eSewa Integration

#### Configuration
1. **Environment Variables**: Add the following to your `.env` file:
   ```
   ESEWA_MERCHANT_CODE=EPAYTEST
   ESEWA_SECRET_KEY=8gBm/:&EnhH.1/q
   ESEWA_PAYMENT_URL=https://rc-epay.esewa.com.np/api/epay/main/v2/form
   ESEWA_VERIFICATION_URL=https://uat.esewa.com.np/api/epay/transaction/status/
   ESEWA_SUCCESS_URL=http://yourdomain.com/esewa/success
   ESEWA_FAILURE_URL=http://yourdomain.com/esewa/failure
   ```

2. **Configuration File**: Settings are stored in `config/esewa.php`. You can adjust tax, service charge, and delivery charge as needed.

#### Usage Examples

**Initiating a Payment**
```php
use App\Services\EsewaService;

$esewaService = new EsewaService();
$amount = 1000; // NPR
$transactionId = $esewaService->generateTransactionId();
$productName = "Product Purchase";

$formData = $esewaService->getPaymentFormData($amount, $transactionId, $productName);
$paymentUrl = $esewaService->getPaymentUrl();

// Redirect user to eSewa payment page with form data
```

**Verifying Payment (Callback)**
```php
$base64Data = request()->query('data');
$verificationResult = $esewaService->verifyPayment($base64Data);

if ($verificationResult['success']) {
    // Payment successful, update order status
    $transactionId = $verificationResult['data']['transaction_uuid'];
    $amount = $verificationResult['data']['total_amount'];
} else {
    // Handle payment failure
}
```

#### Routes
- `GET /esewa/checkout` - Show payment form
- `POST /esewa/pay` - Initiate payment
- `GET /esewa/success` - Handle successful payment callback
- `GET /esewa/failure` - Handle failed payment

### 13.2 Khalti Integration

#### Configuration
1. **Environment Variables**: Add to `.env`:
   ```
   KHALTI_SECRET_KEY_TEST=test_secret_key_XXXX
   KHALTI_PUBLIC_KEY_TEST=test_public_key_XXXX
   KHALTI_SECRET_KEY=your_live_secret_key
   KHALTI_PUBLIC_KEY=your_live_public_key
   KHALTI_TEST_MODE=true
   KHALTI_BASE_URL=https://a.khalti.com/api/v2/
   KHALTI_RETURN_URL=http://yourdomain.com/khalti/callback
   KHALTI_WEBSITE_URL=http://yourdomain.com
   ```

2. **Configuration File**: `config/khalti.php` contains all settings including currency conversion (NPR to paisa).

#### Usage Examples

**Initiating a Payment**
```php
use App\Services\KhaltiService;

$khaltiService = new KhaltiService();
$amount = 500; // NPR
$orderId = $khaltiService->generateOrderId();
$orderName = "Order #123";

$result = $khaltiService->initiatePayment($amount, $orderId, $orderName);

if ($result['success']) {
    // Redirect user to Khalti payment page
    return redirect($result['payment_url']);
} else {
    // Handle error
    return back()->with('error', $result['message']);
}
```

**Verifying Payment**
```php
$pidx = request()->query('pidx');
$verificationResult = $khaltiService->verifyPayment($pidx);

if ($verificationResult['success'] && $verificationResult['status'] === 'Completed') {
    // Payment completed successfully
    $transactionId = $verificationResult['transaction_id'];
    $amount = $verificationResult['amount'];
} else {
    // Payment failed or pending
}
```

#### Routes
- `GET /khalti/checkout` - Show payment form
- `POST /khalti/pay` - Initiate payment
- `GET /khalti/callback` - Handle Khalti callback

### 13.3 Testing Payment Gateways

**eSewa Sandbox**
- Use merchant code: `EPAYTEST`
- Use secret key: `8gBm/:&EnhH.1/q`
- Test cards: Not required for eSewa sandbox (simulated payments)

**Khalti Sandbox**
- Enable test mode: `KHALTI_TEST_MODE=true`
- Use test credentials from Khalti merchant dashboard
- Test mobile number: `9800000000` (for Khalti sandbox)

### 13.4 Troubleshooting Payment Issues

**Common Issues**
1. **Signature mismatch (eSewa)**: Ensure secret key matches and signature generation uses correct message format.
2. **Payment verification fails**: Check that verification URLs are correct and accessible from your server.
3. **Callback not received**: Verify return URLs are publicly accessible and correctly configured in payment gateway dashboard.
4. **Amount mismatch**: Khalti expects amount in paisa (multiply NPR by 100).

**Logs**
- Check `storage/logs/laravel.log` for payment gateway errors.
- Enable debug logging in `.env`: `APP_DEBUG=true`

---

## 14. Troubleshooting & Support

### 14.1 Common Issues & Solutions

**Issue 1: Cannot Login**
- **Solution**: Check username/password, reset if needed
- **Check**: Caps Lock, network connection
- **Contact**: Administrator for account unlock

**Issue 2: Product Not Found in POS**
- **Solution**: Verify product is active and in stock
- **Check**: Store filter, product status
- **Action**: Add product or adjust stock

**Issue 3: Incorrect Stock Levels**
- **Solution**: Perform physical count and adjustment
- **Check**: Pending purchases, unprocessed sales
- **Review**: Stock movement history for discrepancies

**Issue 4: Slow System Performance**
- **Solution**: Clear browser cache, restart browser
- **Check**: Internet connection speed
- **Action**: Close unnecessary tabs, reduce data load

**Issue 5: Printer Not Working**
- **Solution**: Check printer connection and paper
- **Verify**: Printer driver installation
- **Test**: Print test page from system

### 14.2 Data Backup & Recovery
**Regular Backups:**
- **Automatic**: Daily system backups
- **Manual**: Export data before major changes
- **Cloud**: Backup to secure cloud storage

**Data Recovery:**
- Restore from latest backup
- Contact technical support
- Use audit trail to reconstruct data

### 14.3 Getting Help
**Support Channels:**
- **In-app Help**: Click help icon in top-right
- **Email Support**: support@yourcompany.com
- **Phone Support**: +977-9807669230
- **Live Chat**: Available during business hours
- **Knowledge Base**: Online documentation

**When Contacting Support:**
1. Describe the issue clearly
2. Include error messages (screenshot if possible)
3. Note what you were trying to do
4. Provide your user ID and store location
5. Mention steps already tried

### 14.4 System Maintenance
**Daily Tasks:**
- Verify backup completion
- Check system alerts
- Review pending transactions
- Clear completed sessions

**Weekly Tasks:**
- Review low stock reports
- Process pending adjustments
- Clean up old data
- Update product information

**Monthly Tasks:**
- Run financial reports
- Reconcile inventory
- Review user permissions
- System performance check

---

## Appendix A: Keyboard Shortcuts Reference

### General Shortcuts
- `Ctrl + N`: New record (product, sale, purchase)
- `Ctrl + S`: Save current form
- `Ctrl + F`: Search within page
- `Ctrl + P`: Print current view
- `Esc`: Cancel/close dialog

### POS Specific
- `F1` - `F10`: Function keys as described in section 6.3
- `Enter`: Add selected product to cart
- `Tab`: Move between fields
- `Delete`: Remove selected item from cart
- `Ctrl + Enter`: Complete sale

### Navigation
- `Alt + D`: Dashboard
- `Alt + P`: Products
- `Alt + S`: Sales
- `Alt + I`: Inventory
- `Alt + R`: Reports

---

## Appendix B: Glossary of Terms

**SKU (Stock Keeping Unit)**: Unique identifier for each product variant
**Barcode**: Machine-readable product identification
**POS (Point of Sale)**: System for processing retail transactions
**PO (Purchase Order)**: Document sent to supplier to order goods
**Inventory Turnover**: How quickly inventory is sold and replaced
**Reorder Level**: Minimum stock quantity triggering reorder
**FIFO (First In, First Out)**: Inventory valuation method
**COGS (Cost of Goods Sold)**: Direct costs of producing goods sold
**Gross Margin**: Difference between revenue and COGS
**Net Profit**: Revenue minus all expenses

---

## Appendix C: Quick Start Checklist for New Users

### Day 1: System Familiarization
- [ ] Login and explore dashboard
- [ ] Review main menu structure
- [ ] Update your profile information
- [ ] Practice with test products in POS

### Week 1: Basic Operations
- [ ] Process 10+ sales transactions
- [ ] Add 5+ new products
- [ ] Create 2+ purchase orders
- [ ] Generate basic reports

### Month 1: Advanced Features
- [ ] Set up customer categories
- [ ] Configure automated reports
- [ ] Perform physical inventory count
- [ ] Train another team member

---

## Appendix D: Security Best Practices

1. **Password Management**
   - Use strong, unique passwords
   - Change passwords every 90 days
   - Never share login credentials
   - Enable two-factor authentication if available

2. **Access Control**
   - Assign minimum necessary permissions
   - Review user access regularly
   - Disable inactive accounts
   - Log out when leaving workstation

3. **Data Protection**
   - Regular backups
   - Secure physical access to servers
   - Encrypt sensitive data
   - Follow company data policies

---

## Need Additional Help?

For further assistance, contact our support team:

**Technical Support**: support@appantech.com.np
**Phone**: +977 9807669230
**Website**: [appantech.com.np](https://appantech.com.np)
**Business Hours**: 9:00 AM - 6:00 PM (NPT), Sunday-Friday

*This documentation was last updated: April 25, 2026*
*System Version: 2.1.0*