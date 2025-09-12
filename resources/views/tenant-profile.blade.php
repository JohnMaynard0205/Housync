@extends('layouts.app')

@section('title', 'Profile')

@section('content')

            <div class="dashboard-content">
                <!-- Welcome Section -->
                <div class="tenant-welcome" style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                    <h2 style="color: #1f2937; margin: 0 0 10px 0;">Profile Information</h2>
                    <p style="color: #6b7280; margin: 0;">Manage your account details and settings</p>
                </div>

                <!-- Profile Stats Cards -->
                <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 30px;">
                    <div class="stat-card tenant-stat-card" style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                        <div class="stat-info">
                            <div class="stat-label" style="color: #6b7280; font-size: 14px; margin-bottom: 8px;">Full Name</div>
                            <div class="stat-value" style="color: #1f2937; font-size: 24px; font-weight: 600;">{{ $tenant->name }}</div>
                        </div>
                        <div class="stat-icon" style="color: #10b981; font-size: 32px;">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    
                    @if($assignment)
                    <div class="stat-card tenant-stat-card" style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                        <div class="stat-info">
                            <div class="stat-label" style="color: #6b7280; font-size: 14px; margin-bottom: 8px;">Unit</div>
                            <div class="stat-value" style="color: #1f2937; font-size: 24px; font-weight: 600;">{{ $assignment->unit->unit_number }}</div>
                        </div>
                        <div class="stat-icon" style="color: #10b981; font-size: 32px;">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card tenant-stat-card" style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                        <div class="stat-info">
                            <div class="stat-label" style="color: #6b7280; font-size: 14px; margin-bottom: 8px;">Lease Status</div>
                            <div class="stat-value" style="color: #10b981; font-size: 20px; font-weight: 600;">{{ ucfirst($assignment->status) }}</div>
                        </div>
                        <div class="stat-icon" style="color: #10b981; font-size: 32px;">
                            <i class="fas fa-file-contract"></i>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Account Information -->
                <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); border-left: 4px solid #10b981;">
                    <h3 style="color: #1f2937; margin: 0 0 24px 0; font-size: 20px; font-weight: 600;">Account Information</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                        <div style="space-y: 16px;">
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">Email Address</label>
                                <div style="color: #1f2937; font-size: 16px; padding: 12px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">{{ $tenant->email }}</div>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">Password</label>
                                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <span id="passwordDisplay" style="color: #1f2937; flex: 1;">••••••••••</span>
                                    <button onclick="togglePasswordVisibility()" id="passwordToggle" style="background: #10b981; border: none; color: white; cursor: pointer; padding: 8px 12px; border-radius: 6px; transition: all 0.2s;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($assignment && $assignment->documents_verified)
                                    <button onclick="showChangePasswordModal()" style="background: #059669; border: none; color: white; cursor: pointer; padding: 8px 12px; border-radius: 6px; transition: all 0.2s;">
                                        <i class="fas fa-edit"></i> Change
                                    </button>
                                    @endif
                                </div>
                                @if(!$assignment || !$assignment->documents_verified)
                                <div style="margin-top: 8px; padding: 8px 12px; background: #fef3c7; border-radius: 6px; border-left: 4px solid #f59e0b;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-info-circle" style="color: #d97706;"></i>
                                        <span style="color: #92400e; font-size: 14px; font-weight: 500;">Password change will be available after your documents are verified by the landlord.</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div style="space-y: 16px;">
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">Account Type</label>
                                <div style="color: #1f2937; font-size: 16px; padding: 12px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">{{ ucfirst($tenant->role) }}</div>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">Account Status</label>
                                <div style="padding: 12px; background: #d1fae5; border-radius: 8px; border: 1px solid #a7f3d0;">
                                    <span style="color: #065f46; font-weight: 600; font-size: 16px;">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">Member Since</label>
                            <div style="color: #6b7280; font-size: 16px; padding: 12px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">{{ $tenant->created_at->format('F Y') }}</div>
                        </div>
                        @if($tenant->updated_at)
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">Last Updated</label>
                            <div style="color: #6b7280; font-size: 16px; padding: 12px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">{{ $tenant->updated_at->diffForHumans() }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($rfidCards && $rfidCards->count() > 0)
                <!-- RFID Access Cards -->
                <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); border-left: 4px solid #10b981;">
                    <h3 style="color: #1f2937; margin: 0 0 24px 0; font-size: 20px; font-weight: 600;">
                        <i class="fas fa-id-card" style="color: #10b981; margin-right: 8px;"></i>
                        RFID Access Cards
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                        @foreach($rfidCards as $card)
                        <div style="background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; right: 0; width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1); border-radius: 0 0 0 60px;"></div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                                <div>
                                    <div style="font-weight: 700; color: #1f2937; font-size: 18px; margin-bottom: 4px;">
                                        <i class="fas fa-credit-card" style="color: #10b981; margin-right: 8px;"></i>
                                        Card #{{ $card->card_number }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        @if($card->status === 'active')
                                        <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-check-circle" style="margin-right: 4px;"></i>Active
                                        </span>
                                        @else
                                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                            <i class="fas fa-times-circle" style="margin-right: 4px;"></i>Inactive
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <i class="fas fa-key" style="color: #10b981; font-size: 24px; opacity: 0.7;"></i>
                            </div>
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
                                    <div>
                                        <span style="color: #6b7280; font-weight: 500;">Issued:</span>
                                        <div style="color: #1f2937; font-weight: 600;">{{ $card->created_at->format('M d, Y') }}</div>
                                    </div>
                                    @if($card->expires_at)
                                    <div>
                                        <span style="color: #6b7280; font-weight: 500;">Expires:</span>
                                        <div style="color: #1f2937; font-weight: 600;">{{ $card->expires_at->format('M d, Y') }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                </div>
            </div>
        </main>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 30px; width: 90%; max-width: 500px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h3 style="margin: 0; color: #1f2937; font-size: 20px; font-weight: 600;">
                    <i class="fas fa-lock" style="color: #10b981; margin-right: 8px;"></i>
                    Change Password
                </h3>
                <button onclick="hideChangePasswordModal()" style="background: none; border: none; color: #6b7280; cursor: pointer; font-size: 24px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="changePasswordForm">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">Current Password</label>
                    <input type="password" id="currentPassword" name="current_password" required
                           style="width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: #f9fafb;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">New Password</label>
                    <input type="password" id="newPassword" name="new_password" required minlength="8"
                           style="width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: #f9fafb;">
                    <div style="margin-top: 4px; font-size: 12px; color: #6b7280;">Minimum 8 characters required</div>
                </div>
                
                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">Confirm New Password</label>
                    <input type="password" id="confirmPassword" name="new_password_confirmation" required minlength="8"
                           style="width: 100%; padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: #f9fafb;">
                </div>
                
                <div id="passwordError" style="display: none; margin-bottom: 16px; padding: 12px; background: #fee2e2; border-radius: 8px; border-left: 4px solid #ef4444;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-exclamation-circle" style="color: #dc2626;"></i>
                        <span id="passwordErrorText" style="color: #dc2626; font-size: 14px; font-weight: 500;"></span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="hideChangePasswordModal()" 
                            style="padding: 12px 24px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                        Cancel
                    </button>
                    <button type="submit" id="changePasswordBtn"
                            style="padding: 12px 24px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-save" style="margin-right: 4px;"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            const sidebarState = localStorage.getItem('sidebarExpanded');
            if (sidebarState === 'true') {
                sidebar.classList.add('collapsed');
            }
            
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                const isExpanded = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarExpanded', isExpanded);
            });
        });
        
        function handleLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Create and submit logout form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function uploadProfilePhoto() {
            alert('Profile photo upload functionality will be implemented in a future update.');
        }

        function togglePasswordVisibility() {
            const passwordDisplay = document.getElementById('passwordDisplay');
            const passwordToggle = document.getElementById('passwordToggle');
            
            if (passwordDisplay.textContent === '••••••••••••') {
                // Show actual password
                passwordDisplay.textContent = 'Loading...';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
                
                // Fetch the actual password
                fetch('{{ route("tenant.get-password") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.password) {
                        passwordDisplay.textContent = data.password;
                    } else {
                        passwordDisplay.textContent = 'Unable to retrieve password';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    passwordDisplay.textContent = 'Error loading password';
                });
            } else {
                passwordDisplay.textContent = '••••••••••••';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }

        function showChangePasswordModal() {
            document.getElementById('changePasswordModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function hideChangePasswordModal() {
            document.getElementById('changePasswordModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            
            // Reset form
            document.getElementById('changePasswordForm').reset();
            document.getElementById('passwordError').style.display = 'none';
        }

        // Handle password change form submission
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const submitBtn = document.getElementById('changePasswordBtn');
            const errorDiv = document.getElementById('passwordError');
            const errorText = document.getElementById('passwordErrorText');
            
            // Hide previous errors
            errorDiv.style.display = 'none';
            
            // Validate passwords match
            if (newPassword !== confirmPassword) {
                errorText.textContent = 'New passwords do not match.';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Validate password length
            if (newPassword.length < 8) {
                errorText.textContent = 'New password must be at least 8 characters long.';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 4px;"></i>Updating...';
            
            // Make AJAX request
            fetch('{{ route("tenant.update-password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success
                    hideChangePasswordModal();
                    
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #d1fae5; color: #065f46; padding: 16px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 1001; border-left: 4px solid #10b981;';
                    successDiv.innerHTML = '<i class="fas fa-check-circle" style="margin-right: 8px;"></i>' + data.success;
                    document.body.appendChild(successDiv);
                    
                    setTimeout(() => {
                        successDiv.remove();
                    }, 5000);
                    
                } else if (data.error) {
                    // Error from server
                    errorText.textContent = data.error;
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorText.textContent = 'An error occurred while updating your password.';
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save" style="margin-right: 4px;"></i>Update Password';
            });
        });
    </script>
@endsection