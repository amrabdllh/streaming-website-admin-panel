document.addEventListener('DOMContentLoaded', function() {
    // Genre filtering functionality
    const genreButtons = document.querySelectorAll('.genre-btn');
    const videoCards = document.querySelectorAll('.video-card');
    const searchInput = document.getElementById('searchInput');

    // Genre filter event listeners
    genreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const selectedGenre = this.getAttribute('data-genre');
            
            // Update active button
            genreButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter videos
            filterVideos(selectedGenre, searchInput.value);
        });
    });

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const activeGenre = document.querySelector('.genre-btn.active').getAttribute('data-genre');
            
            filterVideos(activeGenre, searchTerm);
        });
    }

    // Filter videos function
    function filterVideos(genre, searchTerm) {
        videoCards.forEach(card => {
            const cardGenres = card.getAttribute('data-genres'); // Contains all genres separated by |
            const cardTitle = card.getAttribute('data-title');
            
            let showCard = true;
            
            // Genre filter
            if (genre !== 'all') {
                // Check if the selected genre is in the card's genres
                const genreList = cardGenres ? cardGenres.split('|').map(g => g.trim().toLowerCase()) : [];
                const selectedGenre = genre.toLowerCase().trim();
                if (!genreList.includes(selectedGenre)) {
                    showCard = false;
                }
            }
            
            // Search filter
            if (searchTerm && !cardTitle.includes(searchTerm)) {
                showCard = false;
            }
            
            // Show/hide card
            if (showCard) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Video card hover effects
    videoCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Lazy loading for images
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
});
