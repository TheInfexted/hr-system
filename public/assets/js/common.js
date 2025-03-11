// Initialize all date pickers on the page
document.addEventListener('DOMContentLoaded', function() {
    // Initialize datepickers
    initializeDatepickers();
});

function initializeDatepickers() {
    // Find all inputs with the date-picker class
    const datepickers = document.querySelectorAll('.date-picker');
    
    // Initialize each datepicker
    datepickers.forEach(input => {
        new AirDatepicker(input, {
            dateFormat: 'yyyy-MM-dd',
            autoClose: true,
            position: 'bottom left',
            // You can add more configurations here
        });
    });
}