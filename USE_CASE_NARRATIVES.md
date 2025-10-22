# HouSync Property Management System - Use Case Narratives

## Overview
HouSync is a comprehensive property management system that facilitates the management of rental properties, tenant assignments, staff management, RFID security systems, and document verification. The system supports multiple user roles including Super Admin, Landlord, Tenant, and Staff with role-based access control.

---

## Use Case 01: Landlord Registration
**Use Case**: Landlord Registration  
**Actors**: Landlord  
**Pre-condition**: The Landlord who wants to put their properties on the website is not registered yet.  
**Post-Condition**: The Landlord is successfully registered and can now add properties  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The landlord opens the HouSync website and selects "Register as a landlord." | The system displays a registration form with fields for basic information and business documents. |
| The Landlord fills out the registration form and submits it. | The system validates the input data and checks for any missing fields. |
| The Landlord confirms the registration details. | The system creates a new Landlord account and stores the information in the database. |
| The Landlord receives a registration confirmation message. | The system sends a confirmation and redirects to the pending approval screen. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The Landlord enters a registered email. | 1a. The system displays an error message. |
| 2a. The Landlord fails to fill out required fields. | 2a. The system prompts the Landlord to complete them before submitting. |
| 3a. The Landlord cancels the registration process. | 3a. The system discards the entered information and returns the Landlord to the login screen. |

---

## Use Case 02: Super Admin Approval
**Use Case**: Super Admin Accepts or Declines Landlord  
**Actors**: Super Admin  
**Pre-condition**: The landlord's account is pending approval from the admin.  
**Post-Condition**: The Landlord's account is approved by the super admin.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The Super Admin checks the legitimacy of the landlord's documents. | The system will display the documents that have been passed by the landlord. |
| The Super Admin verified the legitimacy of the documents sent by the landlord. | The system has a small box on the display to mark it check. |
| The Super admin accepts the landlord | The system saves the Landlord's data, and marks the account as "Approved". |
| The Super admin sees the registered landlord on the dashboard. | The system updates the landlord status and sends notification. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The superadmin cannot verify the authenticity of the documents. | 1a. The system displays a message to notify the landlord immediately |
| 2a. The super admin notifies the landlord of its documents | 2a. The system sends a message to the landlord regarding the authenticity of its documents. |

---

## Use Case 03: Landlord Property Management
**Use Case**: Landlord Property Management  
**Actors**: Landlord  
**Pre-condition**: The Landlord has been approved by the Super Admin  
**Post-Condition**: The Landlord can login and manage properties  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The Landlord opens the HouSync via browser | The system displays the login screen. |
| The Landlord enters the login credentials provided by the admin. | The system validates the credentials. |
| The Landlord successfully logs in. | The system redirects the user to the Landlord Dashboard |
| The Landlord creates apartments and units | The system provides property creation forms and stores property data. |
| The Landlord uploads property images and documents | The system uploads files to Supabase storage and stores URLs. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 2a. The Landlord enters invalid credentials. | 2a. The system displays an error message prompting the Landlord to check the entered details. |

---

## Use Case 04: Tenant Assignment Management
**Use Case**: Assign Tenant to Unit  
**Actors**: Landlord  
**Pre-condition**: The landlord has available units and wants to assign a tenant.  
**Post-Condition**: The tenant is successfully assigned to a unit with generated credentials  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The landlord navigates to the tenant assignment section. | The system displays available units and current assignments |
| The landlord selects a tenant to assign to a unit. | The system shows the tenant assignment form. |
| The landlord fills in tenant details and lease information. | The system validates the lease terms and tenant information. |
| The landlord submits an assignment. | The system creates a tenant account and generates login credentials. |
| The landlord receives the tenant credentials. | The system displays the credentials and marks the unit as occupied. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The selected unit is not available. | 1a. The system displays an error and shows only available units. |
| 2a. The lease dates are invalid. | 2a. The system prompts for valid lease start and end dates. |
| 3a. The landlord wants to reassign a vacated tenant. | 3a. The system allows reassignment with new lease terms. |

---

## Use Case 05: Document Upload and Verification
**Use Case**: Tenant Document Management  
**Actors**: Tenant, Landlord  
**Pre-condition**: The tenant has been assigned to a unit and needs to upload required documents.  
**Post-Condition**: Documents are uploaded, verified, and stored in the system.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The tenant logs in and navigates to the document upload section. | The system displays the document upload interface. |
| The tenant selects document types and uploads files | The system validates file types and sizes. |
| The tenant receives a prompt that the files are pending. | The system marks the tenants files as pending. |
| The Landlord approves the uploaded files. | The system updates the tenant and landlord status of the tenant documents. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The Landlord rejects the uploaded documents. | 1a. The system displays the decline in the tenant documents page. |

---

## Use Case 06: Staff Management
**Use Case**: Staff Assigning  
**Actors**: Landlord  
**Pre-condition**: The Landlord is logged in and has access to the Landlord Dashboard  
**Post-Condition**: The Landlord can view and manage their assigned staff.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The Landlord navigates to "Staff" | The system displays a list of existing staff, if there are any. |
| The Landlord clicks on add a Staff | The system displays detailed information about the staff hired. |
| The landlord receives the newly created login credentials for the Staff. | The staff can login and see the unit they are assigned to |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The landlord reassigns an already existing staff. | 1a. The system displays the staff list indicating that the staff has been reassigned |

---

## Use Case 07: Tenant Payment via HouSync
**Use Case**: Tenant Payment  
**Actors**: Tenant  
**Pre-condition**: The tenant is logged in to their existing account  
**Post-Condition**: The tenant's monthly payment is settled for the month  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The tenant views their outstanding balance. | The system displays a tenants outstanding balance |
| The patient navigates to the payment section. | The system displays the different ways the tenant can pay. |
| The patient pays via their chosen mode of payment | The system records the payment and notifies the landlord. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The tenant is disrupted during the transaction. | 1a. The system cancels the transaction to avoid void transactions. |

---

## Use Case 08: Tenant Login
**Use Case**: Tenant Login  
**Actors**: Tenant, Landlord  
**Pre-condition**: The Landlord has created and assigned the tenant to a unit.  
**Post-Condition**: The Landlord is given login credentials for the Tenant.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The Landlord creates and assigns a tenant to a unit. | The system provides login credentials for the tenant. |
| The tenant logs in using the received login credentials. | The tenant is logged into the tenant dashboard |

---

## Use Case 09: RFID Security Management
**Use Case**: Manage RFID Access Cards  
**Actors**: Landlord  
**Pre-condition**: The property has an RFID security system installed.  
**Post-Condition**: RFID cards are assigned to tenants with proper access control.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The landlord navigates to the security management section. | The system displays RFID cards and access logs |
| The landlord selects a tenant to assign RFID card | The system shows available tenants and card assignments from. |
| The landlord enters card UID and assignment details. | The system validates card information and tenant assignment. |
| The landlord submits the card assignment. | The system links the cards to the tenant and activates access. |
| The landlord monitors access logs. | The system displays real-time access attempts and results. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The card UID is already assigned. | 1a. The system displays an error and prevents duplicate assignment. |
| 2a. The tenants lease has expired. | 2a. The system automatically declines access and logs the attempt. |
| 3a. An unauthorized card is used | 3a. The system denies access and logs the security event. |

---

## Use Case 10: Access Control and Logging
**Use Case**: Monitor Property Access  
**Actors**: Landlord, ESP32 hardware  
**Pre-condition**: The RFID system is operational and cards are assigned.  
**Post-Condition**: All access attempts are logged and monitored.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| A tenant swipes their RFID card at the entrance. | The ESP32 hardware reads the card and sends data to the system. |
| The system verifies the card against the database. | The system checks card validity, tenant status, and lease expiration. |
| The system grants or denies access. | The system sends a response to ESP32 and logs the access attempt. |
| The Landlord reviews access logs. | The system displays detailed access history with filtering options. |
| The Landlord identifies security issues. | The system provides analytics and security reports. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The card is expired or inactive. | 1a. The system denies access and logs the reason. |
| 2a. The Tenant's lease has terminated. | 2a. The system automatically deactivates the card and denies access. |
| 3a. Multiple failed access attempts occur. | 3a. The system flags the card for review and notifies the landlord. |

---

## Use Case 11: Unit Management and Status Tracking
**Use Case**: Manage Property Units  
**Actors**: Landlords  
**Pre-condition**: The landlord has properties with multiple units.  
**Post-Condition**: Units are properly managed with accurate status tracking  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The landlord navigates to the units management section. | The system displays all units with their current status. |
| The landlord creates new units for an apartment. | The system provides unit creation form with detailed specifications. |
| The landlord updates unit information and status. | The system validates changes and updates the database. |
| The landlord filters and searches units. | The system provides advanced filtering by status, type, and availability. |
| The landlord views unit statistics. | The system displays occupancy rates and unit performance metrics. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The landlord tries to create a duplicate unit number. | 1a. The system prevents creation and displays an error. |
| 2a. The landlord wants to mark a unit for maintenance. | 2a. The system allows status change and prevents new assignments. |
| 3a. The landlord needs to update unit pricing. | 3a. The system allows pricing updates while maintaining lease agreements. |

---

## Use Case 12: Maintenance Request Management
**Use Case**: Handle Maintenance Requests  
**Actors**: Tenant, Staff, Landlord  
**Pre-condition**: Tenants can submit maintenance requests for their units  
**Post-Condition**: Maintenance requests are processed and completed.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The tenant submits a maintenance request. | The system creates a request record and notifies the landlord. |
| The landlord reviews the maintenance request. | The system displays request details and priority level. |
| The landlord assigns staff to handle request | The system updates the request status and notifies assigned staff. |
| The staff member updates requests for progress. | The system tracks progress and updates the request status. |
| The maintenance is completed. | The system marks the request as completed and notifies all parties. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The request is urgent and requires immediate attention. | 1a. The system flags it as high priority and sends immediate notifications. |
| 2a. he staff member cannot complete the request. | 2a. The system allows reassignment to different staff or external contractors. |
| 3a. The tenant cancels the maintenance request. | 3a. The system updates the status and notifies all involved parties. |

---

## Use Case 13: Staff Dashboard and Assignment Management
**Use Case**: Staff Access and Management  
**Actors**: Staff  
**Pre-condition**: A staff member has been assigned to units by the landlord.  
**Post-Condition**: Staff can access their dashboard and manage assigned responsibilities.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The staff member logs in with provided credentials. | The system validates credentials and displays staff dashboard. |
| The staff member views assigned units and tasks. | The system displays unit assignments and maintenance requests. |
| The staff member updates task status. | The system tracks progress and notifies relevant parties. |
| The staff member completes assigned tasks. | The system marks tasks as completed and updates records. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The staff member needs to request additional resources. | 1a. The system provides resource request forms and notifies the landlord. |

---

## Use Case 14: Tenant Dashboard and Services
**Use Case**: Tenant Access to Services  
**Actors**: Tenant  
**Pre-condition**: Tenant has been assigned to a unit and has active credentials.  
**Post-Condition**: Tenants can access all available services and manage their account.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The tenant logs in with their credentials. | The system validates credentials and displays a tenant dashboard. |
| The tenant views their unit and lease information. | The system displays current lease details and unit specifications. |
| The tenant uploads required documents. | The system provides a document upload interface with validation. |
| The tenant submits maintenance requests. | The system creates maintenance tickets and tracks progress. |
| The tenant views their payment history and lease status. | The system displays financial information and lease terms. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The tenant's lease has expired. | 1a. The system restricts access and prompts for lease renewal. |
| 2a. The tenant needs to update their contact information. | 2a. The system allows profile updates and notifies the landlord. |
| 3a. The tenant wants to request lease termination. | 3a. The system provides termination request form and processes the requests. |

---

## Use Case 15: Super Admin User Management
**Use Case**: Manage All System Users  
**Actors**: Super Admin  
**Pre-condition**: Super Admin has full system access and administrative privileges.  
**Post-Condition**: All users are properly managed with appropriate access levels.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The Super admin navigates to the user management section. | The system displays all users with their roles and status. |
| The Super Admin reviews pending landlord registration. | The system shows registration details and required documents. |
| The Super Admin creates new user accounts. | The system updates user status and sends notifications. |
| The Super Admin manages user permissions and access. | The system provides user creation forms with role assignment. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The super admin needs to suspend a user account. | 1a. The system allows account suspension with reason tracking. |
| 2a. The super admin wants to reset the user password. | 2a. The system provides password reset functionality. |
| 3a. The Super Admin needs to audit user activities. | 3a. The system provides comprehensive audit logs and reports. |

---

## Use Case 16: System Analytics and Reporting
**Use Case**: Generate Property Management Reports  
**Actors**: Landlord, Super Admin  
**Pre-condition**: The system has collected sufficient data for analysis.  
**Post-Condition**: Comprehensive reports are generated for decision making.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The user navigates to the analytics and reporting section. | The system displays available report types and metrics. |
| The user selects specific reports and parameters. | The system provides filtering and date range options. |
| The user generates occupancy reports | The system calculates occupancy rates and trends. |
| The user views financial performance metrics. | The system displays revenue, expenses, and profitability data. |
| The user exports for external use. | The system provides export options in various formats. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The user needs custom report parameters. | 1a. The system allows custom report creation with flexible parameters. |
| 2a. The user wants to schedule automated reports. | 2a. The system provides report scheduling and email delivery. |
| 3a. The user needs real-time dashboard updates. | 3a. The system provides live data updates and notifications. |

---

## Use Case 17: System Security and Access Control
**Use Case**: Maintain System Security  
**Actors**: All users, System  
**Pre-condition**: The system implements security measures and access controls.  
**Post-Condition**: System security is maintained and unauthorized access is prevented.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| Users attempt to access restricted areas. | The system validates user permissions and role-based access. |
| The system monitors login attempts and activities. | The system logs all access attempts and suspicious activities. |
| The system detects potential security threats. | The system implements security measures and notifies administrators. |
| Users change their passwords or security settings. | The system validates changes and updates security records. |
| The system performs security audits and updates. | The system maintains security compliance and updates security protocols. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. Multiple failed login attempts are detected. | 1a. The system temporarily locks the account and notifies the user. |
| 2a. Unusual access patterns are identified. | 2a. The system flags the activity and requires additional verification. |
| 3a. Security vulnerabilities are discovered. | 3a. The system implements immediate security patches and updates. |

---

## Use Case 18: Data Backup and Recovery
**Use Case**: Data Backup and Recovery  
**Actors**: System Administrator  
**Pre-condition**: The system has implemented backup and recovery procedures.  
**Post-Condition**: Data is safely backed up and can be recovered if needed.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The system performs automated daily backups. | The system creates encrypted backups of all critical data. |
| The administrator monitors backup status. | The system provides backup status reports and notifications. |
| The system validates backup integrity. | The system performs integrity checks on backup files. |
| The administrator tests recovery procedures. | The system allows recovery testing in isolated environments. |
| The system archives old backup data. | The system maintains backup retention policies and archives old data. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. A backup fails to complete successfully. | 1a. The system retries the backup and notifies administrators. |
| 2a. Data recovery is needed due to system failure. | 2a. The system provides step-by-step recovery procedures |
| 3a. Backup storage space is running low. | 3a. The system notifies administrators and implements cleanup procedures. |

---

## Use Case 19: System Integration and API Management
**Use Case**: Manage External System Integrations  
**Actors**: System Administrator, External Systems  
**Pre-condition**: The system has API endpoints and integration capabilities.  
**Post-Condition**: External systems can communicate with HouSync securely.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| External systems authenticate with the API. | The system validates API credentials and permissions. |
| External systems request data or perform actions. | The system processes API requests and returns appropriate responses. |
| The system logs all API interactions. | The system maintains detailed logs of all external system communications. |
| The system monitors API performance and usage. | The system tracks API response times and usage patterns. |
| The system updates API documentation and versions. | The system maintains current API documentation and version control. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. API rate limits are exceeded. | 1a. The system implements rate limiting and returns appropriate error codes. |
| 2a. API authentication fails. | 2a. The system rejects the request and logs the security event. |
| 3a. The external system sends invalid data. | 3a. The system validates data and returns error messages with details. |

---

## Use Case 20: Mobile Application Support
**Use Case**: Provide Mobile Access to System Features  
**Actors**: All users  
**Pre-condition**: Mobile applications are developed and deployed.  
**Post-Condition**: Users can access system features through mobile devices.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| Users download and install the mobile application. | The system provides mobile app download links and installation guides. |
| Users authenticate through the mobile app. | The system validates mobile credentials and provides secure access. |
| Users access system features through mobile interface. | The system provides responsive mobile-optimized interfaces. |
| Users receive push notifications for important events. | The system sends real-time notifications to mobile devices. |
| Users sync data between mobile and web platforms | The system maintains data consistency across all platforms. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. Mobile devices lose internet connection. | 1a. The system provides offline functionality and syncs when connection is restored. |
| 2a. Mobile apps encounter technical issues. | 2a. The system provides error handling and user support options. |
| 3a. Users need to access advanced features on mobile. | 3a. The system provides mobile-optimized versions of advanced features. |

---

## Use Case 21: Payment Processing and Billing Management
**Use Case**: Manage Rent Collection and Payment Processing  
**Actors**: Tenant, Landlord, Payment Gateway  
**Pre-condition**: Tenant has been assigned to a unit with an active lease agreement.  
**Post-Condition**: Rent payments are processed and financial records are updated.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The system generates monthly rent invoices for tenants. | The system creates invoice records with due dates and amounts. |
| The tenant receives payment notification. | The system sends email/SMS notifications about upcoming payments. |
| The tenant selects the payment method and submits payment. | The system validates payment information and processes transactions. |
| The payment gateway processes the transaction. | The system receives payment confirmation and updates records. |
| The landlord receives payment notification. | The system updates financial records and sends confirmation. |
| The tenant views payment history and receipts. | The system displays complete payment history with downloadable receipts. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The tenant's payment fails due to insufficient funds. | 1a. The system notifies the tenant and landlord, marks payment as failed. |
| 2a. The tenant pays a partial amount. | 2a. The system records partial payment and updates outstanding balance. |
| 3a. The tenant requests payment extension. | 3a. The system allows extension requests and notifies the landlord for approval. |
| 4a. The payment is overdue. | 4a. The system applies late fees and sends overdue notifications. |

---

## Use Case 22: Communication and Messaging System
**Use Case**: Enable Communication Between System Users  
**Actors**: Tenant, Landlord, Staff, System  
**Pre-condition**: Users have active accounts and proper permissions.  
**Post-Condition**: Messages are delivered and communication is facilitated.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The user navigates to the messaging section. | The system displays conversation lists and active chats. |
| The user selects a conversation or starts a new message. | The system loads conversation history or creates new threads. |
| The user composes and sends a message. | The system validates message content and delivers it to the recipient. |
| The recipient receives message notification. | The system sends real-time notifications and updates conversation. |
| The recipient reads and responds to the message. | The system marks the message as read and delivers a response. |
| Users can attach files to messages. | The system validates file types and stores attachments securely. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The recipient is offline when a message is sent. | 1a. The system stores messages and delivers them when the recipient comes online. |
| 2a. The user tries to send inappropriate content. | 2a. The system filters content and blocks inappropriate messages. |
| 3a. The user wants to delete a sent message. | 3a. The system allows message deletion within a time limit. |
| 4a. The user needs to search message history. | 4a. The system provides search functionality across all conversations. |

---

## Use Case 23: Maintenance Request Workflow Management
**Use Case**: Manage Complete Maintenance Request Lifecycle  
**Actors**: Tenant, Staff, Landlord  
**Pre-condition**: The maintenance request system is operational and staff are assigned.  
**Post-Condition**: Maintenance requests are completed and all parties are notified.  

### Flow of Events
| Actor Action | System Response |
|--------------|-----------------|
| The tenant submits a maintenance request with details. | The system creates a request record and notifies the landlord immediately. |
| The landlord reviews and assigns priority to the request. | The system updates request priority and notifies assigned staff. |
| The staff member receives assignment notification. | The system sends detailed request information to staff. |
| The staff member updates request status and progress. | The system tracks progress and notifies tenants of updates. |
| The staff member schedules a maintenance visit. | The system sends scheduling confirmation to the tenant. |
| The maintenance work is completed. | The system marks requests as completed and requests tenant confirmation. |
| The tenant confirms completion and rates service. | The system closes requests and stores feedback for reporting. |

### Alternative Scenarios
| Actor Action | System Response |
|--------------|-----------------|
| 1a. The request is urgent and requires immediate attention. | 1a. The system flags as high priority and sends immediate alerts to all parties. |
| 2a. The assigned staff cannot complete the request. | 2a. The system allows reassignment to different staff or external contractors. |
| 3a. The tenant cancels the maintenance request. | 3a. The system updates status and notifies all involved parties. |
| 4a. The maintenance requires follow-up work. | 4a. The system creates follow-up requests and links to original requests. |
| 5a. The tenant is not satisfied with completed work. | 5a. The system allows reopening requests and escalates to the landlord. |

---

## Technical Implementation Notes

### Database Structure
- **Users Table**: Core user authentication with role-based access
- **Profile Tables**: Separate profile tables for each user type (LandlordProfile, TenantProfile, StaffProfile, SuperAdminProfile)
- **Property Management**: Apartments, Units, Properties with image storage
- **Assignment System**: TenantAssignment, StaffAssignment for role-based assignments
- **Document Management**: TenantDocument, LandlordDocument with Supabase storage
- **RFID Security**: RfidCard, AccessLog for security management
- **Maintenance**: MaintenanceRequest for property maintenance tracking

### File Storage
- **Supabase Integration**: All file uploads use Supabase Storage
- **Image Management**: Property images, unit images, and document storage
- **File Validation**: Type and size validation for all uploads
- **URL Management**: Direct URL storage in database for efficient access

### Security Features
- **Role-Based Access Control**: Middleware-based access control
- **RFID Security System**: ESP32 integration for physical access control
- **Document Verification**: Multi-level document approval system
- **Audit Logging**: Comprehensive logging for all system activities

### API Integration
- **RESTful APIs**: Standard REST endpoints for all operations
- **File Upload APIs**: Supabase-integrated file upload endpoints
- **RFID Integration**: ESP32 communication endpoints
- **Mobile Support**: Responsive design and mobile-optimized interfaces

---

*This document represents the comprehensive use case narratives for the HouSync Property Management System, covering all major functionality and user interactions within the system.*
