// Initialize all date pickers on the page
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for all scripts to load, then initialize datepickers
    setTimeout(initializeDatepickers, 100);
});

// Global function to initialize date pickers (can be called manually)
function initializeDatepickers() {
    // Check if AirDatepicker is available
    if (typeof AirDatepicker === 'undefined') {
        console.warn('AirDatepicker library not loaded');
        return;
    }
    
    // Find all inputs with date-picker classes
    const datePickerSelectors = [
        '.date-picker',
        'input[type="date"]', // Convert all HTML5 date inputs
        '.air-datepicker'
    ];
    
    datePickerSelectors.forEach(selector => {
        const datepickers = document.querySelectorAll(selector);
        
        // Initialize each datepicker
        datepickers.forEach(input => {
            // Skip if already initialized
            if (input.hasAttribute('data-air-datepicker')) {
                return;
            }
            
            try {
                // Get current value to preserve it
                const currentValue = input.value;
                
                // Change input type from 'date' to 'text' for AirDatepicker
                if (input.type === 'date') {
                    input.type = 'text';
                }
                
                // Add date-picker class for consistent styling
                input.classList.add('date-picker');
                
                // Initialize AirDatepicker with proper locale handling
                const datepickerOptions = {
                    dateFormat: 'yyyy-MM-dd',
                    autoClose: true,
                    position: 'bottom left',
                    // Preserve existing value
                    selectedDates: currentValue ? [new Date(currentValue)] : []
                };
                
                // Add locale if available
                if (typeof AirDatepicker.locales !== 'undefined' && AirDatepicker.locales.en) {
                    datepickerOptions.locale = AirDatepicker.locales.en;
                } else {
                    // Fallback to default English settings
                    datepickerOptions.locale = {
                        days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                        daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                        daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        today: 'Today',
                        clear: 'Clear',
                        dateFormat: 'yyyy-MM-dd',
                        timeFormat: 'HH:mm',
                        firstDay: 0
                    };
                }
                
                new AirDatepicker(input, datepickerOptions);
                
                // Mark as initialized
                input.setAttribute('data-air-datepicker', 'true');
                
                console.log('Initialized datepicker for:', input.id || input.name || 'unnamed input');
            } catch (error) {
                console.error('Error initializing datepicker:', error);
            }
        });
    });
}
