/**
 * Custom Date Input — format dd-mm-yyyy
 * Replaces all <input type="date"> with text inputs using dd-mm-yyyy mask.
 * Converts dd-mm-yyyy to yyyy-mm-dd on form submission so the server receives the correct format.
 */
(function() {
    'use strict';

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDateInputs);
    } else {
        initDateInputs();
    }

    function initDateInputs() {
        document.querySelectorAll('input[type="date"]').forEach(convertInput);

        // Observe for dynamically added date inputs (modals, etc.)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                m.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.matches && node.matches('input[type="date"]')) {
                            convertInput(node);
                        }
                        if (node.querySelectorAll) {
                            node.querySelectorAll('input[type="date"]').forEach(convertInput);
                        }
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    function convertInput(dateInput) {
        // Skip if already converted
        if (dateInput.dataset.dateConverted) return;
        dateInput.dataset.dateConverted = 'true';

        // Create replacement text input
        var textInput = document.createElement('input');
        textInput.type = 'text';
        textInput.placeholder = 'dd-mm-yyyy';
        textInput.maxLength = 10;
        textInput.autocomplete = 'off';

        // Copy attributes
        if (dateInput.id) textInput.id = dateInput.id;
        if (dateInput.className) textInput.className = dateInput.className;
        if (dateInput.required) textInput.required = true;
        if (dateInput.style.cssText) textInput.style.cssText = dateInput.style.cssText;

        // Copy name — we'll use a hidden input for the real server value
        var fieldName = dateInput.name;
        textInput.name = ''; // Display input has no name (won't be submitted)
        textInput.dataset.dateField = fieldName;

        // Create hidden input for actual server submission (yyyy-mm-dd)
        var hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = fieldName;

        // Convert existing value (yyyy-mm-dd) to display (dd-mm-yyyy)
        if (dateInput.value) {
            var parts = dateInput.value.split('-');
            if (parts.length === 3 && parts[0].length === 4) {
                textInput.value = parts[2] + '-' + parts[1] + '-' + parts[0];
                hiddenInput.value = dateInput.value;
            }
        }

        // Replace the original
        dateInput.parentNode.insertBefore(textInput, dateInput);
        dateInput.parentNode.insertBefore(hiddenInput, dateInput);
        dateInput.remove();

        // Auto-format as user types
        textInput.addEventListener('input', function(e) {
            var val = this.value.replace(/[^0-9]/g, '');
            var formatted = '';

            if (val.length >= 1) {
                formatted = val.substring(0, 2);
            }
            if (val.length >= 3) {
                formatted += '-' + val.substring(2, 4);
            }
            if (val.length >= 5) {
                formatted += '-' + val.substring(4, 8);
            }

            this.value = formatted;

            // Update hidden input with yyyy-mm-dd
            if (formatted.length === 10) {
                var dp = formatted.split('-');
                var day = parseInt(dp[0], 10);
                var month = parseInt(dp[1], 10);
                var year = parseInt(dp[2], 10);

                if (day >= 1 && day <= 31 && month >= 1 && month <= 12 && year >= 1900 && year <= 2100) {
                    hiddenInput.value = dp[2] + '-' + dp[1] + '-' + dp[0];
                    this.style.borderColor = '';
                } else {
                    hiddenInput.value = '';
                    this.style.borderColor = '#e53935';
                }
            } else {
                hiddenInput.value = '';
            }
        });

        // Also handle FormData for AJAX submissions
        textInput.addEventListener('change', function() {
            if (this.value.length === 10) {
                var dp = this.value.split('-');
                hiddenInput.value = dp[2] + '-' + dp[1] + '-' + dp[0];
            }
        });
    }

    // Expose for manual use (e.g., after loading edit modal data)
    window.setDateValue = function(elementId, serverValue) {
        var el = document.getElementById(elementId);
        if (!el) return;

        if (serverValue) {
            var parts = serverValue.split('-');
            if (parts.length === 3 && parts[0].length === 4) {
                el.value = parts[2] + '-' + parts[1] + '-' + parts[0];
                // Update hidden input
                var hidden = el.parentNode.querySelector('input[type="hidden"][name="date"], input[type="hidden"][name="tanggal_lahir"]');
                if (!hidden) {
                    // Try finding by field name from data attribute
                    var fieldName = el.dataset.dateField;
                    if (fieldName) {
                        hidden = el.parentNode.querySelector('input[type="hidden"][name="' + fieldName + '"]');
                    }
                }
                if (hidden) {
                    hidden.value = serverValue;
                }
            }
        }
    };
})();
