// ========================================
// SparePartsNG - Main JavaScript File
// This file handles all the interactive features
// and functionality for the spare parts platform
// ========================================

// When the page loads completely, run the initialization
document.addEventListener('DOMContentLoaded', function() {
    
    // MOBILE MENU - Toggle navigation on small screens
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            // Toggle means switch between active/inactive state
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
    }
    
    // NAVBAR SCROLL EFFECT - Make navbar change when user scrolls down
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            // If user scrolled more than 50px from top
            if (window.scrollY > 50) {
                // Add the scrolled style
                navbar.classList.add('scrolled');
            } else {
                // Remove the style when back at top
                navbar.classList.remove('scrolled');
            }
        });
    }
    
    // SEARCH TABS - Allow user to switch between searching parts and shops
    const searchTabs = document.querySelectorAll('.search-tab');
    
    searchTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            // First, remove active class from all tabs
            for (let i = 0; i < searchTabs.length; i++) {
                searchTabs[i].classList.remove('active');
            }
            
            // Then add active class only to clicked tab
            this.classList.add('active');
            
            // Change the search placeholder text based on which tab
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                if (this.dataset.tab === 'parts') {
                    searchInput.placeholder = 'Search for parts (e.g., brake pads, alternator)...';
                } else if (this.dataset.tab === 'shops') {
                    searchInput.placeholder = 'Search for shops (e.g., Lagos, Ikeja)...';
                }
            }
        });
    });
    
    // FILTER CHIPS - Quick filter buttons like "Brake Pads", "Battery"
    const filterChips = document.querySelectorAll('.filter-chip');
    const searchInput = document.getElementById('searchInput');
    
    for (let i = 0; i < filterChips.length; i++) {
        filterChips[i].addEventListener('click', function() {
            // When user clicks a filter chip, put the search term in the input
            if (searchInput) {
                searchInput.value = this.dataset.search;
                
                // Then automatically submit the search
                const searchForm = document.getElementById('searchForm');
                if (searchForm) {
                    searchForm.dispatchEvent(new Event('submit'));
                }
            }
        });
    }
    
    // SEARCH FORM - Handle when user submits search
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Stop the page from reloading
            e.preventDefault();
            
            // Get what the user typed
            const searchTerm = document.getElementById('searchInput').value;
            const location = '';
            
            // Check if search term is empty
            if (searchTerm.trim() === '') {
                showNotification('Please enter a search term', 'warning');
                return;
            }
            

            // Go to search.php
            window.location.href = 'pages/search.php?q=' + encodeURIComponent(searchTerm);

        });
    }
    
    // LOCATION BUTTON - Get user's GPS location
    const useLocationBtn = document.getElementById('useLocationBtn');
    const showMapBtn = document.getElementById('showMapBtn');
    
    if (useLocationBtn) {
        useLocationBtn.addEventListener('click', getUserLocation);
    }
    
    if (showMapBtn) {
        showMapBtn.addEventListener('click', getUserLocation);
    }
    
    // Category Card Click
    const categoryCards = document.querySelectorAll('.category-card');
    
    for (let i = 0; i < categoryCards.length; i++) {
        categoryCards[i].addEventListener('click', function() {
            // Get the category from the data attribute
            const category = this.dataset.category;
            
            // Get the category name from the h3 tag inside
            const categoryName = this.querySelector('h3').textContent;
            
            // Put category name in search and search
            if (searchInput) {
                searchInput.value = categoryName;
                const searchForm = document.getElementById('searchForm');
                if (searchForm) {
                    searchForm.dispatchEvent(new Event('submit'));
                }
            }
        });
    }
    


// SMOOTH SCROLL - Make navigation links scroll smoothly
const scrollLinks = document.querySelectorAll('a[href^="#"]');

    
    for (let i = 0; i < scrollLinks.length; i++) {
        scrollLinks[i].addEventListener('click', function(e) {
            // Don't use default jump
            e.preventDefault();
            
            // Find the element to scroll to
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                // Get navbar height so we can scroll below it
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = target.offsetTop - navbarHeight;
                
                // Scroll to that position smoothly
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    }
    
    // ANIMATIONS - Show animations when elements come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        for (let i = 0; i < entries.length; i++) {
            const entry = entries[i];
            if (entry.isIntersecting) {
                // Add animation class when element is visible
                entry.target.classList.add('animate-slide');
                // Stop watching this element
                observer.unobserve(entry.target);
            }
        }
    }, observerOptions);
    
    // Start observing cards for animation
    const allCards = document.querySelectorAll('.category-card, .shop-card, .step-card');
    for (let i = 0; i < allCards.length; i++) {
        observer.observe(allCards[i]);
    }
});

// GEOLOCATION FUNCTION - Get user's GPS location coordinates
function getUserLocation() {
    // First check if browser supports geolocation
    if (!navigator.geolocation) {
        showNotification('Geolocation is not supported by your browser', 'error');
        return;
    }
    
    // Show message while getting location
    showNotification('Getting your location...', 'info');
    
    // Get the coordinates
    navigator.geolocation.getCurrentPosition(
        function(position) {
            // Extract latitude and longitude from the position object
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Put the coordinates in the location input field
            const locationInput = document.getElementById('locationInput');
            if (locationInput) {
                locationInput.value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            }
            
            // Hide the placeholder message and show the actual map
            const mapPlaceholder = document.getElementById('mapPlaceholder');
            const mapDisplay = document.getElementById('map');
            
            if (mapPlaceholder) {
                mapPlaceholder.style.display = 'none';
            }
            
            if (mapDisplay) {
                mapDisplay.style.display = 'block';
                
                // Put an embedded Google Map showing location and nearby shops
                mapDisplay.innerHTML = `
                    <iframe 
                        width="100%" 
                        height="400" 
                        style="border:0; border-radius: 12px;" 
                        loading="lazy" 
                        allowfullscreen 
src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.4432365440685!2d3.487362!3d6.618674!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNnwgMzcnMTUuMiJOIDPCsDI5JzE0LjMiRS!5e0!3m2!1sen!2sng!4v1725161234567!5m2!1sen!2sng" style="border:0; border-radius: 12px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </iframe>
                    <div class="map-info">
                        <p><i class="fas fa-map-marker-alt"></i> Your Location</p>
                        <p>Searching for nearby spare parts shops...</p>
                    </div>
                `;
            }
            
            // Show success message
            showNotification('Location found! Showing nearby shops on map.', 'success');
            
            // Load the shop cards with contact information
            loadNearbyShops(lat, lng);
        },
        function(error) {
            // Handle different types of errors
            let errorMessage = 'Unable to get location';
            
            if (error.code === error.PERMISSION_DENIED) {
                errorMessage = 'Location permission denied';
            } else if (error.code === error.POSITION_UNAVAILABLE) {
                errorMessage = 'Location information unavailable';
            } else if (error.code === error.TIMEOUT) {
                errorMessage = 'Location request timed out';
            }
            
            showNotification(errorMessage, 'error');
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

// LOAD NEARBY SHOPS - Get shops near the user's location and display them
function loadNearbyShops(userLat, userLng) {
    // Create an array with all the shops and their locations
    const shops = [
        {
            name: 'Lagos Auto Parts Center',
            address: '15 Allen Avenue, Ikeja, Lagos',
            phone: '08012345678',
            email: 'contact@lagosautoparts.com',
            specialty: 'Engine & Transmission',
            rating: 4.8,
            lat: 6.5244,
            lng: 3.3792
        },
        {
            name: 'Abuja Motor Parts Hub',
            address: '45 Gwarinpa Estate, Abuja',
            phone: '08023456789',
            email: 'contact@abujaautoparts.com',
            specialty: 'Brakes & Suspension',
            rating: 5.0,
            lat: 9.0765,
            lng: 7.3986
        },
        {
            name: 'Port Harcourt Spare Parts',
            address: '25 Trans Woji Road, Port Harcourt',
            phone: '08034567890',
            email: 'contact@phspareparts.com',
            specialty: 'Electrical & Body Parts',
            rating: 4.2,
            lat: 4.7774,
            lng: 7.0134
        },
        {
            name: 'Ibadan Car Center',
            address: 'Ring Road, Ibadan',
            phone: '08045678901',
            email: 'contact@ibadancarcenter.com',
            specialty: 'All Parts',
            rating: 4.5,
            lat: 7.3775,
            lng: 3.9470
        },
        {
            name: 'Kano Motors Limited',
            address: 'Sabon Gari Road, Kano',
            phone: '08056789012',
            email: 'contact@kanomotors.com',
            specialty: 'Engine & Electrical',
            rating: 4.6,
            lat: 12.0022,
            lng: 8.5919
        }
    ];
    
    // Update shop cards with contact details
    const shopCards = document.querySelectorAll('.shop-card');
    shops.forEach((shop, index) => {
        if (shopCards[index]) {
            // Add contact info to the card
            const existingContact = shopCards[index].querySelector('.shop-contact');
            if (!existingContact) {
                const contactDiv = document.createElement('div');
                contactDiv.className = 'shop-contact';
                contactDiv.innerHTML = `
                    <p class="shop-phone"><i class="fas fa-phone"></i> ${shop.phone}</p>
                    <p class="shop-email"><i class="fas fa-envelope"></i> ${shop.email}</p>
                    <a href="https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${shop.lat},${shop.lng}" target="_blank" class="btn-directions">
                        <i class="fas fa-directions"></i> Get Directions
                    </a>
                `;
                shopCards[index].appendChild(contactDiv);
            }
        }
    });
    
    showNotification('Shop contact details loaded. You can now call or email shops!', 'success');
}

// View Parts - SIMPLIFIED (href natural)
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.btn-view-shop');
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Force navigation (backup)
            window.location.href = this.getAttribute('href');
        });
    });
});

// Perform Search Function
function performSearch(searchTerm, location) {
    // Show loading state
    showNotification(`Searching for "${searchTerm}"...`, 'info');
    
    // In production, this would call backend API
    // For demo, we'll simulate results
    
    setTimeout(() => {
        // Scroll to results section
        const resultsSection = document.getElementById('shops');
        if (resultsSection) {
            const navbarHeight = document.querySelector('.navbar').offsetHeight;
            window.scrollTo({
                top: resultsSection.offsetTop - navbarHeight,
                behavior: 'smooth'
            });
        }
        
        showNotification(`Found results for "${searchTerm}"`, 'success');
    }, 1000);
}

// Show Notification/Toast Message
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    // Set icon based on type
    let icon = 'info-circle';
    if (type === 'success') icon = 'check-circle';
    if (type === 'error') icon = 'exclamation-circle';
    if (type === 'warning') icon = 'exclamation-triangle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#2ea043' : type === 'error' ? '#f85149' : type === 'warning' ? '#d29922' : '#58a6ff'};
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 10000;
        animation: slideInRight 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Add animation keyframes to document
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Export functions for global use
window.getUserLocation = getUserLocation;
window.performSearch = performSearch;
window.showNotification = showNotification;
