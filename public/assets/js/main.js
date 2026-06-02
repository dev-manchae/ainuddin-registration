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
            
            const showStatus = function(state, message) {
                autosaveStatus.style.display = 'inline-flex';
                autosaveStatus.className = 'autosave-indicator ' + state;
                
                let icon = '';
                if (state === 'saving') {
                    icon = '<span class="autosave-spinner" style="display:inline-block; width:10px; height:10px; border:2px solid currentColor; border-right-color:transparent; border-radius:50%; animation: spin 0.8s linear infinite; margin-right: 5px;"></span> ';
                } else if (state === 'success') {
                    icon = '✓ ';
                } else if (state === 'error') {
                    icon = '⚠️ ';
                }
                
                autosaveStatus.innerHTML = icon + message;
                
                if (state === 'success') {
                    // Hide success status after 3 seconds
                    setTimeout(function() {
                        if (autosaveStatus.classList.contains('success')) {
                            autosaveStatus.style.opacity = '0';
                            setTimeout(function() {
                                if (autosaveStatus.style.opacity === '0') {
                                    autosaveStatus.style.display = 'none';
                                    autosaveStatus.style.opacity = '1';
                                }
                            }, 300);
                        }
                    }, 3000);
                }
            };
            
            let isAutosaving = false;

            const showStatus = function(state, message) {
                if (state === 'saving') {
                    isAutosaving = true;
                } else {
                    isAutosaving = false;
                }
                
                autosaveStatus.style.display = 'inline-flex';
                autosaveStatus.className = 'autosave-indicator ' + state;
                
                let icon = '';
                if (state === 'saving') {
                    icon = '<span class="autosave-spinner" style="display:inline-block; width:10px; height:10px; border:2px solid currentColor; border-right-color:transparent; border-radius:50%; animation: spin 0.8s linear infinite; margin-right: 5px;"></span> ';
                } else if (state === 'success') {
                    icon = '✓ ';
                } else if (state === 'error') {
                    icon = '⚠️ ';
                }
                
                autosaveStatus.innerHTML = icon + message;
                
                if (state === 'success') {
                    // Hide success status after 3 seconds
                    setTimeout(function() {
                        if (autosaveStatus.classList.contains('success')) {
                            autosaveStatus.style.opacity = '0';
                            setTimeout(function() {
                                if (autosaveStatus.style.opacity === '0') {
                                    autosaveStatus.style.display = 'none';
                                    autosaveStatus.style.opacity = '1';
                                }
                            }, 300);
                        }
                    }, 3000);
                }
            };

            const triggerAutosave = function() {
                showStatus('saving', 'Menyimpan draf...');
                
                const formData = new FormData(stepForm);
                const url = formAction + '&ajax=1';
                
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(function(response) {
                    if (!response.ok) throw new Error('HTTP error ' + response.status);
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        showStatus('success', 'Draf disimpan');
                        document.querySelectorAll('.upload-dropzone.uploading').forEach(function(z) {
                            z.classList.remove('uploading');
                        });
                    } else {
                        showStatus('error', data.error || 'Gagal menyimpan draf');
                        document.querySelectorAll('.upload-dropzone.uploading').forEach(function(z) {
                            z.classList.remove('uploading');
                        });
                    }
                })
                .catch(function(err) {
                    console.error('Autosave error:', err);
                    showStatus('error', 'Gagal menyambung ke pelayan');
                    document.querySelectorAll('.upload-dropzone.uploading').forEach(function(z) {
                        z.classList.remove('uploading');
                    });
                });
            };
            
            const queueAutosave = function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(triggerAutosave, 1200); // 1.2 second debounce
            };
            
            // Listen to inputs, textareas, and select elements
            const formElements = stepForm.querySelectorAll('input, select, textarea');
            formElements.forEach(function(el) {
                // File inputs should save immediately on change rather than debounced keyup
                if (el.type === 'file') {
                    el.addEventListener('change', function() {
                        const zone = el.closest('.upload-dropzone');
                        if (zone) zone.classList.add('uploading');
                        setTimeout(function() {
                            if (el.value !== '') {
                                triggerAutosave();
                            }
                        }, 50);
                    });
                } else {
                    el.addEventListener('input', queueAutosave);
                    el.addEventListener('change', queueAutosave);
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