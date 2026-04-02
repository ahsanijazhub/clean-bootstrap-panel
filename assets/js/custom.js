/**
 * Admin Template - Custom JavaScript
 */

(function() {
    'use strict';

    // ==========================================================================
    // Toast Notification System (renamed to avoid Bootstrap conflict)
    // ==========================================================================
    window.Toast = {
        container: null,

        init: function() {
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.className = 'notification-container';
                document.body.appendChild(this.container);
            }
        },

        show: function(options) {
            this.init();

            const defaults = {
                type: 'info',
                title: '',
                message: '',
                duration: 4000,
                closable: true
            };

            const settings = { ...defaults, ...options };

            const icons = {
                success: 'fas fa-check',
                error: 'fas fa-times',
                warning: 'fas fa-exclamation',
                info: 'fas fa-info'
            };

            const toast = document.createElement('div');
            toast.className = `notification-toast notification-toast-${settings.type}`;
            toast.innerHTML = `
                <div class="notification-icon">
                    <i class="${icons[settings.type]}"></i>
                </div>
                <div class="notification-content">
                    ${settings.title ? `<div class="notification-title">${settings.title}</div>` : ''}
                    <div class="notification-message">${settings.message}</div>
                </div>
                ${settings.closable ? `
                    <button class="notification-close" type="button">
                        <i class="fas fa-times"></i>
                    </button>
                ` : ''}
            `;

            this.container.appendChild(toast);

            // Close button handler
            if (settings.closable) {
                toast.querySelector('.notification-close').addEventListener('click', () => {
                    this.hide(toast);
                });
            }

            // Auto-hide
            if (settings.duration > 0) {
                setTimeout(() => {
                    this.hide(toast);
                }, settings.duration);
            }

            return toast;
        },

        hide: function(toast) {
            toast.classList.add('notification-toast-hiding');
            setTimeout(() => {
                toast.remove();
            }, 300);
        },

        success: function(message, title = 'Success') {
            return this.show({ type: 'success', title, message });
        },

        error: function(message, title = 'Error') {
            return this.show({ type: 'error', title, message });
        },

        warning: function(message, title = 'Warning') {
            return this.show({ type: 'warning', title, message });
        },

        info: function(message, title = 'Info') {
            return this.show({ type: 'info', title, message });
        }
    };

    // ==========================================================================
    // Sidebar Management
    // ==========================================================================
    window.Sidebar = {
        sidebar: null,
        overlay: null,
        isMobile: false,

        init: function() {
            this.sidebar = document.querySelector('.main-sidebar');
            this.overlay = document.querySelector('.sidebar-overlay');

            if (!this.sidebar) return;

            this.checkMobile();
            this.bindEvents();
            this.initOverlayScrollbars();
        },

        checkMobile: function() {
            this.isMobile = window.innerWidth < 992;
        },

        bindEvents: function() {
            const toggleBtn = document.querySelector('.sidebar-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => this.toggle());
            }

            if (this.overlay) {
                this.overlay.addEventListener('click', () => this.close());
            }

            window.addEventListener('resize', () => {
                this.checkMobile();
                if (!this.isMobile) {
                    this.close();
                }
            });

            // Sub-menu toggle
            const menuToggles = document.querySelectorAll('.nav-link[data-toggle="submenu"]');
            menuToggles.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    const parent = toggle.closest('.nav-item');
                    parent.classList.toggle('menu-open');
                });
            });
        },

        initOverlayScrollbars: function() {
            const scrollContainer = this.sidebar.querySelector('.sidebar-content');
            if (scrollContainer && typeof OverlayScrollbarsGlobal !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars(scrollContainer, {
                    scrollbars: {
                        theme: 'os-theme-light',
                        autoHide: 'leave',
                        autoHideDelay: 400
                    }
                });
            }
        },

        toggle: function() {
            if (this.isMobile) {
                this.sidebar.classList.toggle('sidebar-open');
                this.overlay.classList.toggle('active');
                document.body.style.overflow = this.sidebar.classList.contains('sidebar-open') ? 'hidden' : '';
            } else {
                this.sidebar.classList.toggle('sidebar-expanded');
            }
        },

        open: function() {
            if (this.isMobile) {
                this.sidebar.classList.add('sidebar-open');
                this.overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        },

        close: function() {
            if (this.isMobile) {
                this.sidebar.classList.remove('sidebar-open');
                this.overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    };

    // ==========================================================================
    // User Menu Dropdown
    // ==========================================================================
    window.UserMenu = {
        init: function() {
            const userMenu = document.querySelector('.user-menu');
            if (!userMenu) return;

            const toggle = userMenu.querySelector('.user-menu-toggle');

            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                userMenu.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target)) {
                    userMenu.classList.remove('active');
                }
            });
        }
    };

    // ==========================================================================
    // Form Utilities
    // ==========================================================================
    window.FormUtils = {
        // Validate a form
        validate: function(form) {
            let isValid = true;
            const inputs = form.querySelectorAll('[required]');

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            return isValid;
        },

        // Set loading state on button
        setLoading: function(btn, loading = true) {
            if (loading) {
                btn.classList.add('btn-loading');
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
            } else {
                btn.classList.remove('btn-loading');
                btn.disabled = false;
                if (btn.dataset.originalText) {
                    btn.innerHTML = btn.dataset.originalText;
                }
            }
        },

        // AJAX form submit
        submit: function(form, options = {}) {
            const defaults = {
                url: form.action,
                method: form.method || 'POST',
                onSuccess: null,
                onError: null
            };

            const settings = { ...defaults, ...options };
            const formData = new FormData(form);
            const submitBtn = form.querySelector('[type="submit"]');

            if (submitBtn) {
                this.setLoading(submitBtn, true);
            }

            fetch(settings.url, {
                method: settings.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (submitBtn) {
                    this.setLoading(submitBtn, false);
                }

                if (data.success) {
                    if (settings.onSuccess) {
                        settings.onSuccess(data);
                    }
                } else {
                    if (settings.onError) {
                        settings.onError(data);
                    } else {
                        Toast.error(data.message || 'An error occurred');
                    }
                }
            })
            .catch(error => {
                if (submitBtn) {
                    this.setLoading(submitBtn, false);
                }
                Toast.error('Network error. Please try again.');
                console.error('Form submit error:', error);
            });
        }
    };

    // ==========================================================================
    // Confirmation Dialog
    // ==========================================================================
    window.Confirm = {
        show: function(options) {
            const defaults = {
                title: 'Confirm',
                message: 'Are you sure?',
                confirmText: 'Yes',
                cancelText: 'Cancel',
                confirmClass: 'btn-primary',
                onConfirm: null,
                onCancel: null
            };

            const settings = { ...defaults, ...options };

            // Create modal backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop';
            backdrop.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1050;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.2s ease;
            `;

            backdrop.innerHTML = `
                <div class="confirm-dialog" style="
                    background: #fff;
                    border-radius: 0.5rem;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                    max-width: 400px;
                    width: 90%;
                    animation: slideUp 0.2s ease;
                ">
                    <div style="padding: 1.5rem; border-bottom: 1px solid #e9ecef;">
                        <h5 style="margin: 0; font-size: 1.125rem; font-weight: 600;">${settings.title}</h5>
                    </div>
                    <div style="padding: 1.5rem;">
                        <p style="margin: 0; color: #6c757d;">${settings.message}</p>
                    </div>
                    <div style="padding: 1rem 1.5rem; background: #f8f9fa; border-radius: 0 0 0.5rem 0.5rem; display: flex; justify-content: flex-end; gap: 0.5rem;">
                        <button type="button" class="btn btn-secondary confirm-cancel">${settings.cancelText}</button>
                        <button type="button" class="btn ${settings.confirmClass} confirm-ok">${settings.confirmText}</button>
                    </div>
                </div>
            `;

            document.body.appendChild(backdrop);
            document.body.style.overflow = 'hidden';

            const close = () => {
                backdrop.remove();
                document.body.style.overflow = '';
            };

            backdrop.querySelector('.confirm-cancel').addEventListener('click', () => {
                close();
                if (settings.onCancel) settings.onCancel();
            });

            backdrop.querySelector('.confirm-ok').addEventListener('click', () => {
                close();
                if (settings.onConfirm) settings.onConfirm();
            });

            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    close();
                    if (settings.onCancel) settings.onCancel();
                }
            });
        },

        delete: function(message, onConfirm) {
            this.show({
                title: 'Delete Confirmation',
                message: message || 'Are you sure you want to delete this item?',
                confirmText: 'Delete',
                confirmClass: 'btn-danger',
                onConfirm: onConfirm
            });
        }
    };

    // ==========================================================================
    // Initialize on DOM Ready
    // ==========================================================================
    document.addEventListener('DOMContentLoaded', function() {
        Sidebar.init();
        UserMenu.init();

        // Remove loading state on inputs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });

    // Add animation keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

})();
