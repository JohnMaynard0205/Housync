<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - HouSync</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/messages.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .user-profile {
            position: relative;
            cursor: pointer;
        }
        .profile-dropdown {
            position: absolute;
            top: 110%;
            right: 0;
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            border-radius: 8px;
            min-width: 180px;
            z-index: 9999;
            padding: 8px 0;
            display: none;
        }
        .profile-dropdown.show, .user-profile .profile-dropdown.show {
            display: block !important;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #374151;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.2s;
        }
        .dropdown-item:hover {
            background: #f3f4f6;
        }
        .dropdown-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 6px 0;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-content">
                <div class="nav-items-container">
                    <div class="nav-item" onclick="window.location.href='{{ route('dashboard') }}'">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('units') }}'">
                        <i class="fas fa-building"></i>
                        <span>Units</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('tenants') }}'">
                        <i class="fas fa-users"></i>
                        <span>Tenants</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('billing') }}'">
                        <i class="fas fa-credit-card"></i>
                        <span>Billing</span>
                    </div>
                    <div class="nav-item active">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </div>
                    <div class="nav-item" onclick="window.location.href='{{ route('security') }}'">
                        <i class="fas fa-shield-alt"></i>
                        <span>Security Logs</span>
                    </div>
                </div>
                
                <!-- Logout Button at Bottom -->
                <div class="nav-bottom">
                    <div class="nav-item logout-item" onclick="handleLogout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="header-center">
                    <h1 class="app-title">HouSync</h1>
                </div>
                <div class="header-right">
                    <button class="header-btn">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="header-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="user-profile" id="userProfile">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" alt="Ann Lee" class="profile-avatar">
                        <span class="profile-name">Ann Lee</span>
                        <i class="fas fa-chevron-down"></i>
                        <!-- Dropdown Menu -->
                        <div class="profile-dropdown" id="profileDropdown" style="display: none;">
                            <a href="#" class="dropdown-item"><i class="fas fa-user"></i> My Profile</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-cog"></i> Account Settings</a>
                            <a href="{{ route('dashboard') }}" class="dropdown-item"><i class="fas fa-th-large"></i> Dashboard</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-history"></i> My Activity</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-bell"></i> Notifications</a>
                            <a href="#" class="dropdown-item"><i class="fas fa-question-circle"></i> Help & Support</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item" onclick="handleLogout(); return false;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Messages Content -->
            <div class="messages-container">
                <!-- Left Panel - Message List -->
                <div class="messages-sidebar">
                    <!-- Messages Header -->
                    <div class="messages-header">
                        <h2>Messages</h2>
                        <div class="message-actions">
                            <button class="compose-btn">
                                <i class="fas fa-plus"></i>
                                Compose
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filters -->
                    <div class="message-filters">
                        <div class="search-bar">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search messages..." id="messageSearch">
                        </div>
                        <div class="filter-tabs">
                            <button class="filter-tab active" data-filter="all">
                                All <span class="count">12</span>
                            </button>
                            <button class="filter-tab" data-filter="unread">
                                Unread <span class="count">3</span>
                            </button>
                            <button class="filter-tab" data-filter="urgent">
                                Urgent <span class="count">2</span>
                            </button>
                        </div>
                    </div>

                    <!-- Message List -->
                    <div class="message-list">
                        <div class="message-item active" data-conversation="1">
                            <div class="message-avatar">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Juan Karlos">
                                <span class="status-indicator urgent"></span>
                            </div>
                            <div class="message-preview">
                                <div class="message-header">
                                    <span class="sender-name">Juan Karlos</span>
                                    <span class="message-time">2 min ago</span>
                                </div>
                                <div class="message-subject">Air conditioning not working</div>
                                <div class="message-snippet">The AC unit in my bedroom has stopped working. It's been...</div>
                                <div class="message-meta">
                                    <span class="message-type urgent">Urgent</span>
                                    <span class="unit-info">Unit 01</span>
                                </div>
                            </div>
                        </div>

                        <div class="message-item unread" data-conversation="2">
                            <div class="message-avatar">
                                <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face" alt="Ana Reyes">
                                <span class="status-indicator online"></span>
                            </div>
                            <div class="message-preview">
                                <div class="message-header">
                                    <span class="sender-name">Ana Reyes</span>
                                    <span class="message-time">1 hour ago</span>
                                </div>
                                <div class="message-subject">Payment confirmation needed</div>
                                <div class="message-snippet">Hi, I made my rent payment yesterday but haven't received...</div>
                                <div class="message-meta">
                                    <span class="message-type general">General</span>
                                    <span class="unit-info">Unit 02</span>
                                </div>
                            </div>
                        </div>

                        <div class="message-item" data-conversation="3">
                            <div class="message-avatar">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="Carlos Mendoza">
                                <span class="status-indicator offline"></span>
                            </div>
                            <div class="message-preview">
                                <div class="message-header">
                                    <span class="sender-name">Carlos Mendoza</span>
                                    <span class="message-time">3 hours ago</span>
                                </div>
                                <div class="message-subject">Noise complaint</div>
                                <div class="message-snippet">There's been loud music from the unit above mine...</div>
                                <div class="message-meta">
                                    <span class="message-type complaint">Complaint</span>
                                    <span class="unit-info">Unit 04</span>
                                </div>
                            </div>
                        </div>

                        <div class="message-item unread" data-conversation="4">
                            <div class="message-avatar">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face" alt="Maria Santos">
                                <span class="status-indicator online"></span>
                            </div>
                            <div class="message-preview">
                                <div class="message-header">
                                    <span class="sender-name">Maria Santos</span>
                                    <span class="message-time">Yesterday</span>
                                </div>
                                <div class="message-subject">Water pressure issue</div>
                                <div class="message-snippet">The water pressure in my bathroom has been very low...</div>
                                <div class="message-meta">
                                    <span class="message-type maintenance">Maintenance</span>
                                    <span class="unit-info">Unit 06</span>
                                </div>
                            </div>
                        </div>

                        <div class="message-item" data-conversation="5">
                            <div class="message-avatar">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&h=150&fit=crop&crop=face" alt="Roberto Cruz">
                                <span class="status-indicator offline"></span>
                            </div>
                            <div class="message-preview">
                                <div class="message-header">
                                    <span class="sender-name">Roberto Cruz</span>
                                    <span class="message-time">2 days ago</span>
                                </div>
                                <div class="message-subject">Lease renewal inquiry</div>
                                <div class="message-snippet">I wanted to discuss the renewal of my lease which expires...</div>
                                <div class="message-meta">
                                    <span class="message-type general">General</span>
                                    <span class="unit-info">Unit 08</span>
                                </div>
                            </div>
                        </div>

                        <div class="message-item urgent unread" data-conversation="6">
                            <div class="message-avatar">
                                <img src="https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?w=150&h=150&fit=crop&crop=face" alt="Lisa Garcia">
                                <span class="status-indicator urgent"></span>
                            </div>
                            <div class="message-preview">
                                <div class="message-header">
                                    <span class="sender-name">Lisa Garcia</span>
                                    <span class="message-time">3 days ago</span>
                                </div>
                                <div class="message-subject">Emergency: Water leak</div>
                                <div class="message-snippet">There's a major water leak in my kitchen ceiling...</div>
                                <div class="message-meta">
                                    <span class="message-type urgent">Urgent</span>
                                    <span class="unit-info">Unit 05</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Conversation View -->
                <div class="conversation-panel">
                    <!-- Conversation Header -->
                    <div class="conversation-header">
                        <div class="contact-info">
                            <div class="contact-avatar">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face" alt="Juan Karlos">
                                <span class="status-indicator urgent"></span>
                            </div>
                            <div class="contact-details">
                                <h3>Juan Karlos</h3>
                                <span class="contact-meta">Unit 01 • +63 912 345 6789</span>
                            </div>
                        </div>
                        <div class="conversation-actions">
                            <button class="action-btn">
                                <i class="fas fa-phone"></i>
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-video"></i>
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Messages Thread -->
                    <div class="messages-thread">
                        <div class="thread-date">
                            <span>Today</span>
                        </div>

                        <div class="message tenant-message">
                            <div class="message-content">
                                <div class="message-bubble">
                                    <p>Hi Ann, the air conditioning unit in my bedroom has stopped working completely. It's been getting really hot and uncomfortable. Could someone please check it out as soon as possible?</p>
                                    <span class="message-timestamp">2:15 PM</span>
                                </div>
                            </div>
                        </div>

                        <div class="message tenant-message">
                            <div class="message-content">
                                <div class="message-bubble">
                                    <p>I also wanted to mention that this is the second time this month. The technician came last week but the problem started again.</p>
                                    <span class="message-timestamp">2:16 PM</span>
                                </div>
                            </div>
                        </div>

                        <div class="message admin-message">
                            <div class="message-content">
                                <div class="message-bubble">
                                    <p>Hi Juan, I'm sorry to hear about the AC issue. I'll contact our maintenance team right away and have someone check it today. This shouldn't be happening repeatedly.</p>
                                    <span class="message-timestamp">2:45 PM</span>
                                </div>
                            </div>
                        </div>

                        <div class="message tenant-message">
                            <div class="message-content">
                                <div class="message-bubble">
                                    <p>Thank you so much! I really appreciate the quick response. Will someone contact me to schedule a time?</p>
                                    <span class="message-timestamp">2:47 PM</span>
                                </div>
                            </div>
                        </div>

                        <div class="typing-indicator">
                            <div class="typing-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <span class="typing-text">Ann is typing...</span>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="message-input-container">
                        <div class="input-toolbar">
                            <button class="toolbar-btn">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button class="toolbar-btn">
                                <i class="fas fa-image"></i>
                            </button>
                            <button class="toolbar-btn">
                                <i class="fas fa-smile"></i>
                            </button>
                        </div>
                        <div class="input-area">
                            <textarea 
                                placeholder="Type your message..." 
                                id="messageInput"
                                rows="3"
                            ></textarea>
                            <div class="input-actions">
                                <div class="priority-selector">
                                    <select id="messagePriority">
                                        <option value="normal">Normal</option>
                                        <option value="urgent">Urgent</option>
                                        <option value="high">High Priority</option>
                                    </select>
                                </div>
                                <button class="send-btn" id="sendMessage">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Enhanced menu toggle functionality with persistence
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            // Restore sidebar state from localStorage
            const sidebarState = localStorage.getItem('sidebarExpanded');
            if (sidebarState === 'true') {
                sidebar.classList.add('collapsed');
            }
            
            // Toggle sidebar and save state
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                const isExpanded = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarExpanded', isExpanded);
            });
        });
        
        // Logout functionality
        function handleLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Create a form to submit the logout request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Message search functionality
        document.getElementById('messageSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const messages = document.querySelectorAll('.message-item');
            
            messages.forEach(message => {
                const senderName = message.querySelector('.sender-name').textContent.toLowerCase();
                const subject = message.querySelector('.message-subject').textContent.toLowerCase();
                const snippet = message.querySelector('.message-snippet').textContent.toLowerCase();
                
                if (senderName.includes(searchTerm) || subject.includes(searchTerm) || snippet.includes(searchTerm)) {
                    message.style.display = 'flex';
                } else {
                    message.style.display = 'none';
                }
            });
        });

        // Filter tabs functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                const messages = document.querySelectorAll('.message-item');
                
                messages.forEach(message => {
                    if (filter === 'all') {
                        message.style.display = 'flex';
                    } else if (filter === 'unread' && message.classList.contains('unread')) {
                        message.style.display = 'flex';
                    } else if (filter === 'urgent' && message.classList.contains('urgent')) {
                        message.style.display = 'flex';
                    } else {
                        message.style.display = 'none';
                    }
                });
            });
        });

        // Message item selection
        document.querySelectorAll('.message-item').forEach(item => {
            item.addEventListener('click', function() {
                // Update active message
                document.querySelectorAll('.message-item').forEach(m => m.classList.remove('active'));
                this.classList.add('active');
                
                // Mark as read
                this.classList.remove('unread');
                
                // Update conversation panel (would fetch real conversation data in actual app)
                const conversationId = this.dataset.conversation;
                console.log('Loading conversation:', conversationId);
            });
        });

        // Send message functionality
        document.getElementById('sendMessage').addEventListener('click', function() {
            const messageInput = document.getElementById('messageInput');
            const messageText = messageInput.value.trim();
            const priority = document.getElementById('messagePriority').value;
            
            if (messageText) {
                // Create new message element
                const messagesThread = document.querySelector('.messages-thread');
                const newMessage = document.createElement('div');
                newMessage.className = 'message admin-message';
                newMessage.innerHTML = `
                    <div class="message-content">
                        <div class="message-bubble">
                            <p>${messageText}</p>
                            <span class="message-timestamp">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                    </div>
                `;
                
                // Remove typing indicator and add message
                const typingIndicator = document.querySelector('.typing-indicator');
                messagesThread.insertBefore(newMessage, typingIndicator);
                
                // Clear input
                messageInput.value = '';
                
                // Scroll to bottom
                messagesThread.scrollTop = messagesThread.scrollHeight;
                
                // Show success feedback
                const sendBtn = this;
                sendBtn.innerHTML = '<i class="fas fa-check"></i>';
                sendBtn.style.background = '#10b981';
                
                setTimeout(() => {
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    sendBtn.style.background = '';
                }, 1000);
            }
        });

        // Auto-resize textarea
        document.getElementById('messageInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Quick action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i').className;
                let action = 'Action';
                
                if (icon.includes('phone')) action = 'Call Tenant';
                else if (icon.includes('video')) action = 'Video Call';
                else if (icon.includes('info-circle')) action = 'View Profile';
                
                alert(`${action} functionality will be implemented.`);
            });
        });

        // Compose button
        document.querySelector('.compose-btn').addEventListener('click', function() {
            alert('Compose new message functionality will be implemented.');
        });

        // Toolbar buttons
        document.querySelectorAll('.toolbar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const icon = this.querySelector('i').className;
                let action = 'Action';
                
                if (icon.includes('paperclip')) action = 'Attach File';
                else if (icon.includes('image')) action = 'Add Image';
                else if (icon.includes('smile')) action = 'Add Emoji';
                
                alert(`${action} functionality will be implemented.`);
            });
        });

        // Auto-scroll to bottom of conversation
        document.addEventListener('DOMContentLoaded', function() {
            const messagesThread = document.querySelector('.messages-thread');
            messagesThread.scrollTop = messagesThread.scrollHeight;
        });

        // Profile dropdown logic
        document.addEventListener('DOMContentLoaded', function() {
            const userProfile = document.getElementById('userProfile');
            const profileDropdown = document.getElementById('profileDropdown');
            let dropdownOpen = false;
            userProfile.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownOpen = !dropdownOpen;
                profileDropdown.style.display = dropdownOpen ? 'block' : 'none';
            });
            document.addEventListener('click', function() {
                if (dropdownOpen) {
                    profileDropdown.style.display = 'none';
                    dropdownOpen = false;
                }
            });
        });
    </script>
    
    
</body>
</html> 