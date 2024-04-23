document.addEventListener('DOMContentLoaded', function() {
    const link = document.getElementById('serviceLink'); // Get the link
    const proximityThreshold = 50; // pixels

    document.addEventListener('mousemove', function(event) {
        const { clientX, clientY } = event; // Get the mouse position

        const linkRect = link.getBoundingClientRect(); // Get link position and size
        const linkX = linkRect.left + window.scrollX + (linkRect.width / 2); // X center of link
        const linkY = linkRect.top + window.scrollY + (linkRect.height / 2); // Y center of link

        // Calculate distance from the center of the link to the mouse position
        const distance = Math.sqrt(Math.pow(linkX - clientX, 2) + Math.pow(linkY - clientY, 2));

        if (distance < proximityThreshold) {
            // Mouse is within proximity, apply styles
            link.style.textDecoration = 'underline'; // Underline the link
            link.style.cursor = 'pointer'; // Change cursor to pointer
        } else {
            // Mouse is outside the proximity, remove styles
            link.style.textDecoration = 'none';
        }
    });
});