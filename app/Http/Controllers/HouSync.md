HouSync

HouseSync is a web-based management system designed for boarding house owners to efficiently track tenant information, monitor unit occupancy, and manage billing for rent, electricity, and water with RFID keycard integration, allowing tenants to scan in and out at the building's entrance for additional security.

Proponents:

·       de los Reyes, Joshua Caleb

·       Fernandez, Sean Archer

·       Canete, John Maynard

MUST HAVES

Architecture

-        Web

-        Database

-        ESP32

-        RFID Module

System Features (Global Features Available to All Roles)

·       User Authentication & Authorization
-        Role-based access control (Super Admin, Landlord, Tenant, Staff)
-        Secure login/logout functionality
-        Password management and reset capabilities
-        Session management

·       Document Management System
-        File upload and storage (Supabase integration)
-        Document type categorization
-        Document verification workflow
-        Document download and access control
-        Support for multiple file formats (PDF, JPG, PNG)

·       Property Exploration & Discovery
-        Public property listing page (Explore)
-        Property search and filtering
-        Property detail pages with gallery
-        Tenant application system for properties

·       Notification System (Infrastructure Ready)
-        In-app notification framework
-        Email notification capability
-        SMS notification capability (infrastructure in place)

·       Reporting & Export
-        CSV export functionality
-        Data filtering and search capabilities
-        Historical data tracking

·       System Settings Management
-        Global system configuration
-        Email template management
-        Default rate settings
-        Feature toggles
-        Dark mode support

·       Activity Logging & Audit Trail
-        System activity logs
-        Access log tracking
-        User action auditing
-        Data backup capabilities

Super Admin – Main Features

·       Approve or reject Land Lord registrations and manage their accounts.
-        Review landlord registration applications
-        View and verify landlord documents
-        Approve/reject landlord accounts with reason tracking
-        Manage landlord account status

·       Monitor all landlord and property data across the system.
-        View all landlords and their properties
-        Monitor system-wide statistics
-        Access all apartments and units
-        View tenant assignments across all landlords

·       Manage global system settings (e.g., email templates, default rates).
-        Configure system-wide settings
-        Manage email templates
-        Set default rates and configurations
-        Control feature availability
-        Manage notification settings

·       Moderate disputes or flagged issues from any tenant or landlord.
-        Access to all user accounts
-        View all tenant and landlord data
-        System-wide moderation capabilities

·       User Management
-        Create, edit, and delete user accounts
-        Manage user roles and permissions
-        View user activity and statistics

Land Lord – Main Features

-        Can manage multiple boarding houses in one dashboard

·   	Tenant & Unit Management
-        Create, update, and delete tenant and unit records
-        Assign occupants, set unit capacity, rental pricing, and availability (Vacant, Occupied, Reserved)
-        Manage tenant documents (IDs, leases, contracts)
-        Assign RFID keycards to tenants and manage entry/exit logs for security tracking
-        Automated assigning to stay buttons
-        Bulk unit creation and management
-        Unit status management (Available, Occupied, Maintenance)
-        Tenant assignment workflow with approval system
-        Tenant reassignment capabilities
-        Tenant history tracking and export

·       Property Management
-        Create and manage multiple properties (apartments, condominiums, townhouses, houses, duplexes)
-        Property details management (address, amenities, contact info)
-        Property image gallery management
-        Property status management (Active, Inactive, Maintenance)
-        Auto-generate units for properties
-        Property sorting and filtering

·       Staff Management
-        Add and manage staff members
-        Assign staff to specific units
-        Role-based staff assignment (Plumber, Technician, Maintenance, etc.)
-        Staff assignment tracking
-        Staff status management

·       Dashboard & Reports
-        Monitor monthly income, occupancy status, and vacant units
-        Export reports to CSV (Tenant History)
-        Access system activity logs
-        View property and unit statistics
-        Revenue tracking and analytics

·       RFID Logging System
-        Provides logging system on property entry
-        Landlords can assign and revoke RFID keycards
-        Two RFID Scanners for entry and exit (single-scanner toggle system)
-        WiFi enabled RFID Scanners
-        Access log viewing and filtering
-        Card status management (Active/Inactive)
-        Card reassignment capabilities
-        Real-time access monitoring

·       Tenant Application Management
-        Review tenant applications for properties
-        Approve or reject tenant applications
-        View tenant documents during application review
-        Manage application status

Staff – Main Features (Assigned by the Landlord; Role-Specific Access)

·   	Role-Based Task Assignment
-        Each staff member is assigned a specific role (e.g., Plumber, Technician, Maintenance)
-        They can view tasks/tickets assigned to them (e.g., fix Unit A's leak or check electrical wiring in Unit B)
-        Tasks are mapped to specific units and are viewable on their dashboard
-        View assigned unit information
-        Mark assignments as completed
-        View assignment history

·   	View-Only Access to Assigned Units
-        Can view unit info (e.g., tenant name, room number, issue reported), but cannot modify tenant data
-        See task status (e.g., pending, in-progress, completed)
-        Access to assigned unit details

·   	Profile Management
-        View personal profile information
-        Update password
-        View assignment details

Tenant – Main Features

·       Notifications & Alerts
-        Infrastructure ready for SMS/email alerts for due dates, overdue balances, and payment confirmations
-        In-app alerts for admin updates and account actions (framework in place)

·   	Profile Dashboard
-        View personal info, assigned unit, move-in date, and co-occupants
-        Check lease status and update contact info (with admin approval)
-        View assigned property and unit details
-        Access to personal documents

·       Document Management
-        Upload personal documents (IDs, proof of income, employment contracts, bank statements, etc.)
-        View uploaded documents
-        Delete personal documents
-        Document type categorization

·       Property Application
-        Browse available properties (Explore page)
-        Apply for properties
-        View application status
-        Submit application with personal information

·       Lease Information
-        View lease details (start date, end date, rent amount)
-        Access lease documents
-        View assigned unit information

·       Password Management
-        Update account password
-        Secure password change functionality

·       RFID Logging System
-        Allows tenant access to properties
-        View RFID card assignment status (if assigned)

MISSING FEATURES (Not Yet Implemented)

·       Billing & Payment Tracking
-        Input monthly rent, water, and electricity bills
-        Adjust rates and manage billing cycles
-        Track paid/unpaid balances, mark partial payments, and upload proof
-        Generate and download PDF invoices or receipts
-        View payment history by tenant or unit
-        Payment due date tracking
-        Overdue balance alerts

·       Communication & Chat Module
-        Real-time messaging between tenants and landlords
-        View full chat history with timestamps per tenant
-        Receive reports and concerns via chat
-        Access/download file attachments (e.g., photos, receipts)
-        Staff-tenant direct communication for maintenance tasks
-        Ticketing system for maintenance requests

·       Advanced Reporting
-        PDF report generation
-        Financial reports and analytics
-        Occupancy trend reports
-        Revenue forecasting

·       Notification System (Full Implementation)
-        SMS alerts for due dates and overdue balances
-        Email notifications for payment confirmations
-        Push notifications for bills, notices, or announcements
-        Automated reminder system

·       Maintenance Request Ticketing System
-        Full ticketing system for maintenance requests
-        Priority assignment
-        Status tracking (pending, in-progress, completed)
-        File attachments for maintenance requests
-        Integration with staff assignments

NICE TO HAVE

• Lease Expiry Reminder: Automated alert when tenant lease nears expiration

• Tenant Rating or Notes: Add personal notes or feedback per tenant (basic notes exist, but not rating system)

• Visitor Log: Track external visitors reported by tenants

• Multi-property Support: Manage multiple boarding houses in one dashboard (IMPLEMENTED - landlords can manage multiple properties)

• Push Notifications: Alert tenants of bills, notices, or announcements via SMS (infrastructure ready, needs full implementation)

TO ACCOMPLISH ASAP

• PROJECT ADVISOR
 Ms. Khiara Rubia

• PROJECT COORDINATOR
Mr. Temothy Homecillo

• PANEL CHAIRMAN
Mr. Roderick Bandalan

• PANEL MEMBER
 Engr. Vicente Patallita III
