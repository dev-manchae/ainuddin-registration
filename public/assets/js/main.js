document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================
    // IC NUMBER AUTO-FORMAT
    // Format: XXXXXX-XX-XXXX
    // ==========================
    document.querySelectorAll('[data-ic-format]').forEach(function(input) {
        input.addEventListener('input', function() {
            formatICInput(this);
        });
    });

    // ==========================
    // PHONE NUMBER FORMATTING
    // Formats phone numbers dynamically with spaces
    // ==========================
    document.querySelectorAll('[data-phone-clean]').forEach(function(input) {
        formatPhoneInput(input);
        input.addEventListener('input', function() {
            formatPhoneInput(this);
        });
    });

    // ==========================
    // HEALTH TOGGLE (Tiada/Ada)
    // Used by step4 and step2
    // Initializes on page load from PHP state only
    // Preserves "Ada" value on toggle switch
    // ==========================
    document.querySelectorAll('.health-toggle').forEach(function(toggle) {
        const radios = toggle.querySelectorAll('input[type="radio"]');
        const textarea = toggle.nextElementSibling;

        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'Tiada') {
                    textarea.readOnly = true;
                    textarea.value = 'Tiada';
                    textarea.style.backgroundColor = '#f1f5f9';
                } else {
                    textarea.readOnly = false;
                    if (textarea.value === 'Tiada') {
                        textarea.value = textarea.dataset.original || '';
                    }
                    textarea.placeholder = "Nyatakan butiran...";
                    textarea.style.backgroundColor = 'white';
                }
            });
        });

        // Save original value when switching to "Ada"
        radios.forEach(function(radio) {
            radio.addEventListener('mousedown', function() {
                if (this.value === 'Ada') {
                    textarea.dataset.original = textarea.value;
                }
            });
        });

        // Initialize state from current radio selection
        var selectedRadio = toggle.querySelector('input[type="radio"]:checked');
        if (selectedRadio && selectedRadio.value === 'Tiada') {
            textarea.readOnly = true;
            textarea.value = 'Tiada';
            textarea.style.backgroundColor = '#f1f5f9';
        }
    });

    // ==========================
    // STICKY NAVBAR SCROLL EFFECT
    // ==========================
    const nav = document.querySelector('.top-nav');
    if (nav) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    }

    // ==========================
    // MOBILE MENU TOGGLE
    // ==========================
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });

        var links = navLinks.querySelectorAll('a');
        links.forEach(function(link) {
            link.addEventListener('click', function() {
                navLinks.classList.remove('active');
                menuToggle.classList.remove('active');
            });
        });
    }

    // ==========================
    // ADMIN SIDEBAR TOGGLE
    // ==========================
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
            if (document.body.classList.contains('sidebar-collapsed')) {
                localStorage.setItem('sidebar_state', 'collapsed');
            } else {
                localStorage.setItem('sidebar_state', 'expanded');
            }
        });

        if (localStorage.getItem('sidebar_state') === 'collapsed') {
            document.body.classList.add('sidebar-collapsed');
        }
    }

    // ==========================
    // SCROLL REVEAL ANIMATION
    // ==========================
    var revealElements = document.querySelectorAll('.feature-card, .form-card, .card, .stat-card, .profile-card');
    
    var revealOnScroll = function() {
        revealElements.forEach(function(el) {
            el.classList.add('revealed');
        });
    };

    window.addEventListener('scroll', revealOnScroll);
    // Defer initial reveal to allow browser setup, triggering CSS transitions smoothly
    setTimeout(revealOnScroll, 50); 

    // ==========================
    // INPUT FIELD FOCUS ENHANCEMENT
    // ==========================
    var inputs = document.querySelectorAll('.form-field input, .form-field textarea, .form-field select');
    inputs.forEach(function(input) {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // ==========================
    // STEP WIZARD AUTO-SAVE DRAFTS
    // ==========================
    const stepForm = document.getElementById('stepForm');
    const autosaveStatus = document.getElementById('autosave-status');
    
    if (stepForm && autosaveStatus) {
        const formAction = stepForm.getAttribute('action') || '';
        
        // We only autosave on registration wizard steps 1 to 5
        const isWizardStep = /save_step[1-5]/.test(formAction);
        
        if (isWizardStep) {
            let debounceTimer;
            
            let isAutosaving = false;
            let activeAutosaveToast = null;

            const showAutosaveToast = function(state, message, progress = null) {
                let container = document.getElementById('floating-status-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'floating-status-container';
                    document.body.appendChild(container);
                }

                let toastEl = activeAutosaveToast;
                if (!toastEl) {
                    toastEl = document.createElement('div');
                    container.appendChild(toastEl);
                }

                toastEl.className = 'floating-toast ' + state;
                toastEl.style.opacity = '1';
                toastEl.style.transform = 'translateY(0) scale(1)';

                let title = '';
                let icon = '';
                let body = message || '';
                let showBar = false;

                if (state === 'saving') {
                    title = 'Menyimpan Draf...';
                    icon = '⏳';
                    showBar = true;
                    activeAutosaveToast = toastEl;
                } else if (state === 'success') {
                    title = 'Perubahan Disimpan!';
                    icon = '✅';
                    showBar = true;
                    body = message || 'Semua draf dan fail telah berjaya disimpan.';
                    
                    activeAutosaveToast = null;
                    const currentToast = toastEl;
                    setTimeout(function() {
                        currentToast.style.opacity = '0';
                        currentToast.style.transform = 'translateY(10px) scale(0.95)';
                        setTimeout(function() {
                            currentToast.remove();
                        }, 300);
                    }, 3000);
                } else if (state === 'error') {
                    title = 'Ralat Menyimpan!';
                    icon = '❌';
                    body = message || 'Gagal menyambung ke pelayan.';
                    
                    activeAutosaveToast = null;
                    const currentToast = toastEl;
                    setTimeout(function() {
                        currentToast.style.opacity = '0';
                        currentToast.style.transform = 'translateY(10px) scale(0.95)';
                        setTimeout(function() {
                            currentToast.remove();
                        }, 300);
                    }, 5000);
                }

                toastEl.innerHTML = `
                    <div class="toast-header">
                        <span>${icon} &nbsp;${title}</span>
                    </div>
                    <div class="toast-body">${body}</div>
                    ${showBar ? `
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: ${progress !== null ? progress : (state === 'success' ? 100 : 0)}%;"></div>
                        </div>
                    ` : ''}
                `;
                
                if (state === 'saving' && progress !== null) {
                    const fill = toastEl.querySelector('.progress-bar-fill');
                    if (fill) fill.style.width = progress + '%';
                }
            };

            const showStatus = function(state, message, progress = null) {
                if (state === 'saving') {
                    isAutosaving = true;
                } else {
                    isAutosaving = false;
                }
                
                if (autosaveStatus) {
                    autosaveStatus.style.display = 'none';
                }

                showAutosaveToast(state, message, progress);
            };

            const triggerAutosave = function() {
                showStatus('saving', 'Menyimpan draf...', 0);
                
                const formData = new FormData(stepForm);
                const url = formAction + '&ajax=1';
                
                const xhr = new XMLHttpRequest();
                xhr.open('POST', url);
                
                // Track upload progress
                xhr.upload.onprogress = function(event) {
                    if (event.lengthComputable) {
                        const percentComplete = Math.round((event.loaded / event.total) * 100);
                        let hasFiles = false;
                        if (typeof formData.values === 'function') {
                            for (let value of formData.values()) {
                                if (value instanceof File && value.size > 0) {
                                    hasFiles = true;
                                    break;
                                }
                            }
                        }
                        const msg = hasFiles ? `Memuat naik fail (${percentComplete}%)...` : 'Menyimpan draf...';
                        showStatus('saving', msg, percentComplete);
                    }
                };
                
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.success) {
                                showStatus('success', 'Semua draf dan fail disimpan.');
                                document.querySelectorAll('.upload-dropzone.uploading').forEach(function(z) {
                                    z.classList.remove('uploading');
                                });
                                if (data.documents && typeof window.updateUploadedDocuments === 'function') {
                                    window.updateUploadedDocuments(data.documents);
                                }
                            } else {
                                showStatus('error', data.error || 'Gagal menyimpan draf');
                                document.querySelectorAll('.upload-dropzone.uploading').forEach(function(z) {
                                    z.classList.remove('uploading');
                                });
                            }
                        } catch (e) {
                            showStatus('error', 'Ralat pelayan: Maklum balas tidak sah');
                            document.querySelectorAll('.upload-dropzone.uploading').forEach(function(z) {
                                z.classList.remove('uploading');
                            });
                        }
                    } else {
                        showStatus('error', 'Gagal menyambung ke pelayan (HTTP ' + xhr.status + ')');
                        document.querySelectorAll('.upload-dropzone.uploading').forEach(function(z) {
                            z.classList.remove('uploading');
                        });
                    }
                };
                
                xhr.onerror = function() {
                    showStatus('error', 'Gagal menyambung ke pelayan');
                    document.querySelectorAll('.upload-dropzone.uploading').forEach(function(z) {
                        z.classList.remove('uploading');
                    });
                };
                
                xhr.send(formData);
            };
            
            // Expose globally so step5 can trigger upload when user clicks "Simpan"
            window.triggerAutosaveGlobal = triggerAutosave;
            
            const queueAutosave = function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(triggerAutosave, 1200); // 1.2 second debounce
            };
            
            // Listen to change and input events via delegation on stepForm to handle dynamically added fields
            stepForm.addEventListener('change', function(e) {
                const el = e.target;
                if (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
                    if (el.type === 'file') {
                        // Do not auto-save files on change. User must click "Simpan" on the preview card.
                    } else {
                        queueAutosave();
                    }
                }
            });

            stepForm.addEventListener('input', function(e) {
                const el = e.target;
                if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                    if (el.type !== 'file') {
                        queueAutosave();
                    }
                }
            });

            // Prevent page exit during active save
            window.addEventListener('beforeunload', function(e) {
                if (isAutosaving) {
                    e.preventDefault();
                    e.returnValue = 'Terdapat draf yang sedang disimpan. Adakah anda pasti mahu keluar?';
                    return e.returnValue;
                }
            });
        }
    }

    // ==========================
    // DRAG AND DROP FILE UPLOADS
    // ==========================
    // Click to trigger hidden input
    document.addEventListener('click', function(e) {
        const zone = e.target.closest('.upload-dropzone');
        if (zone) {
            const input = zone.querySelector('input[type="file"]');
            if (input && e.target !== input) {
                input.click();
            }
        }
    });

    // Drag and Drop files handler
    document.addEventListener('dragover', function(e) {
        const zone = e.target.closest('.upload-dropzone');
        if (zone) {
            e.preventDefault();
            zone.classList.add('dragover');
        }
    });
    document.addEventListener('dragenter', function(e) {
        const zone = e.target.closest('.upload-dropzone');
        if (zone) {
            e.preventDefault();
            zone.classList.add('dragover');
        }
    });
    document.addEventListener('dragleave', function(e) {
        const zone = e.target.closest('.upload-dropzone');
        if (zone) {
            e.preventDefault();
            const rect = zone.getBoundingClientRect();
            if (e.clientX < rect.left || e.clientX >= rect.right || e.clientY < rect.top || e.clientY >= rect.bottom) {
                zone.classList.remove('dragover');
            }
        }
    });
    document.addEventListener('drop', function(e) {
        const zone = e.target.closest('.upload-dropzone');
        if (zone) {
            e.preventDefault();
            zone.classList.remove('dragover');
            const input = zone.querySelector('input[type="file"]');
            if (input && e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        }
    });

    // ==========================
    // ACTIVE SESSION TIMEOUT WARNING
    // ==========================
    const sessionModal = document.getElementById('session-timeout-modal');
    if (sessionModal) {
        const keepSessionBtn = document.getElementById('session-keep-btn');
        const logoutSessionBtn = document.getElementById('session-logout-btn');
        const countdownSpan = document.getElementById('session-countdown');
        
        let warningTimer;
        let countdownTimer;
        let secondsRemaining = 180; // 3 minutes countdown
        
        const resetWarningTimer = function() {
            // Only reset if modal is not currently open
            if (sessionModal.style.display === 'flex') return;
            
            clearTimeout(warningTimer);
            // 17 minutes = 1020000 ms
            warningTimer = setTimeout(showSessionWarning, 1020000); 
        };
        
        const showSessionWarning = function() {
            sessionModal.style.display = 'flex';
            secondsRemaining = 180;
            countdownSpan.textContent = formatTime(secondsRemaining);
            
            clearInterval(countdownTimer);
            countdownTimer = setInterval(function() {
                secondsRemaining--;
                countdownSpan.textContent = formatTime(secondsRemaining);
                
                if (secondsRemaining <= 0) {
                    clearInterval(countdownTimer);
                    window.location.href = '?page=logout';
                }
            }, 1000);
        };
        
        const formatTime = function(sec) {
            const m = Math.floor(sec / 60);
            const s = sec % 60;
            return m + ":" + (s < 10 ? "0" : "") + s;
        };
        
        const pingServer = function() {
            fetch('?page=session_ping')
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                if (data.status === 'active') {
                    sessionModal.style.display = 'none';
                    clearInterval(countdownTimer);
                    resetWarningTimer();
                } else {
                    window.location.href = '?page=logout';
                }
            })
            .catch(function() {
                window.location.href = '?page=logout';
            });
        };
        
        if (keepSessionBtn) {
            keepSessionBtn.addEventListener('click', pingServer);
        }
        if (logoutSessionBtn) {
            logoutSessionBtn.addEventListener('click', function() {
                window.location.href = '?page=logout';
            });
        }
        
        // Listen for activity to keep timer reset
        const activityEvents = ['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart'];
        activityEvents.forEach(function(event) {
            document.addEventListener(event, resetWarningTimer);
        });
        
        // Initial call
        resetWarningTimer();
    }
});

// ==========================
// UTILITY: IC AUTO-FORMAT
// Format: XXXXXX-XX-XXXX
// ==========================
function formatICInput(input) {
    var value = input.value.replace(/\D/g, '');
    
    if (value.length > 6) {
        value = value.substring(0, 6) + '-' + value.substring(6, 8) + '-' + value.substring(8, 12);
    }
    
    input.value = value;
}

// ==========================
// UTILITY: PHONE FORMATTER
// Formats phone numbers visually with spaces
// ==========================
function formatPhoneInput(input) {
    var selectionStart = input.selectionStart;
    var oldLength = input.value.length;

    // Get raw digits
    var digits = input.value.replace(/\D/g, '');

    // Strip leading +60, 60 (if followed by a valid digit 1-9) or 0
    if (digits.startsWith('0')) {
        digits = digits.substring(1);
    } else if (digits.startsWith('60') && digits.length > 2 && /^[1-9]/.test(digits.substring(2))) {
        digits = digits.substring(2);
    }

    var formatted = '';
    if (digits.startsWith('11')) {
        // Format: 11 XXXX XXXX (max 10 digits)
        digits = digits.substring(0, 10);
        
        if (digits.length <= 2) {
            formatted = digits;
        } else if (digits.length <= 6) {
            formatted = digits.substring(0, 2) + ' ' + digits.substring(2);
        } else {
            formatted = digits.substring(0, 2) + ' ' + digits.substring(2, 6) + ' ' + digits.substring(6);
        }
    } else if (digits.startsWith('3')) {
        // Format: 3 XXXX XXXX (max 9 digits)
        digits = digits.substring(0, 9);

        if (digits.length <= 1) {
            formatted = digits;
        } else if (digits.length <= 5) {
            formatted = digits.substring(0, 1) + ' ' + digits.substring(1);
        } else {
            formatted = digits.substring(0, 1) + ' ' + digits.substring(1, 5) + ' ' + digits.substring(5);
        }
    } else {
        // Format: XX XXX XXXX (9 digits) or XX XXXX XXXX (10 digits)
        digits = digits.substring(0, 10);

        if (digits.length <= 2) {
            formatted = digits;
        } else if (digits.length <= 9) {
            if (digits.length <= 5) {
                formatted = digits.substring(0, 2) + ' ' + digits.substring(2);
            } else {
                formatted = digits.substring(0, 2) + ' ' + digits.substring(2, 5) + ' ' + digits.substring(5);
            }
        } else {
            formatted = digits.substring(0, 2) + ' ' + digits.substring(2, 6) + ' ' + digits.substring(6);
        }
    }

    input.value = formatted;

    // Adjust selection cursor position
    var newLength = formatted.length;
    var delta = newLength - oldLength;
    var newCursorPos = selectionStart + delta;
    
    if (selectionStart < oldLength) {
        input.setSelectionRange(newCursorPos, newCursorPos);
    }
}

// ==========================
// UTILITY: HEALTH TOGGLE
// Used by step2 and step4 health fields
// Handles Tiada/Ada toggle with readonly textarea
// Preserves "Ada" value when toggling
// ==========================
function handleHealthToggle(selectId, textareaId) {
    var select = document.getElementById(selectId);
    var textarea = document.getElementById(textareaId);
    if (!select || !textarea) return;

    var radios = select.querySelectorAll('input[type="radio"]');
    var selectedRadio = select.querySelector('input[type="radio"]:checked');

    // On initial page load, set state from checked radio
    if (selectedRadio && selectedRadio.value === 'Tiada') {
        textarea.readOnly = true;
        textarea.value = 'Tiada';
        textarea.style.backgroundColor = '#f1f5f9';
    }

    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'Tiada') {
                textarea.readOnly = true;
                textarea.value = 'Tiada';
                textarea.style.backgroundColor = '#f1f5f9';
            } else {
                textarea.readOnly = false;
                if (textarea.value === 'Tiada') {
                    textarea.value = textarea.dataset.original || '';
                }
                textarea.placeholder = "Nyatakan butiran...";
                textarea.style.backgroundColor = 'white';
            }
        });

        // Save original value before switching to "Ada"
        radio.addEventListener('mousedown', function() {
            if (this.value === 'Ada') {
                textarea.dataset.original = textarea.value;
            }
        });
    });
}