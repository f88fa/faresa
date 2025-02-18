// Main JavaScript File
$(document).ready(function() {
    // Add active class to current nav item
    const currentLocation = window.location.pathname;
    $('.nav-link').each(function() {
        const link = $(this).attr('href');
        if (currentLocation.includes(link)) {
            $(this).addClass('active');
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
