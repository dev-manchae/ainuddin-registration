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
    var revealElements = document.querySelectorAll('.feature-card, .form-card, .card');
    
    var revealOnScroll = function() {
        var windowHeight = window.innerHeight;
        revealElements.forEach(function(el) {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    };

    revealElements.forEach(function(el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });

    window.addEventListener('scroll', revealOnScroll);
    revealOnScroll(); 

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