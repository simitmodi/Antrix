// Feature 1: Countdown Timer
function startCountdown(targetDateStr) {
    const timerElement = document.getElementById('countdown-timer');
    if (!timerElement) return;

    const targetDate = new Date(targetDateStr).getTime();

    const interval = setInterval(function() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance < 0) {
            clearInterval(interval);
            timerElement.innerHTML = "Event Started";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        timerElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }, 1000);
}

// Feature 2: Client-side event filter
function filterEvents(type) {
    const cards = document.querySelectorAll('.event-card-item');
    cards.forEach(card => {
        if (type === 'all' || card.dataset.type === type) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Feature 3: Live search on events page
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('event-search');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.event-card-item');
            
            cards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                // Simple regex match for live filter equivalent
                const regex = new RegExp(term, 'i');
                if (regex.test(title)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Feature 6: jQuery AJAX for news search
    const newsSearch = $('#news-search');
    if (newsSearch.length) {
        newsSearch.on('keyup', function() {
            const q = $(this).val();
            $.ajax({
                url: BASE_URL + 'api/events.php',
                type: 'GET',
                data: { action: 'news_search', q: q }, // We will implement news search in api or just events search
                success: function(data) {
                    // This will be handled in news.php directly for cleaner separation, 
                    // or we can call renderNews(data) here if that function is defined.
                    if(typeof renderNews === 'function') {
                        renderNews(data);
                    }
                }
            });
        });
    }

    // Event detail interested button listener
    const intBtn = document.getElementById('interested-btn');
    if (intBtn) {
        intBtn.addEventListener('click', function() {
            const eventId = this.dataset.id;
            markInterested(eventId);
        });
    }
});

// Feature 5: AJAX call using fetch for interest counter
async function markInterested(eventId) {
    try {
        const formData = new FormData();
        formData.append('action', 'interest');
        formData.append('id', eventId);

        const response = await fetch(BASE_URL + 'api/events.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('interest-count').textContent = data.new_count;
        }
    } catch (error) {
        console.error("Error marking interest:", error);
    }
}

// Feature 4: Form validation
function validateEventSubmit(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const titleError = document.getElementById('title-error');
    const dateError = document.getElementById('date-error');
    const imageError = document.getElementById('image-error');
    
    // Clear prev errors
    if(titleError) titleError.textContent = '';
    if(dateError) dateError.textContent = '';
    if(imageError) imageError.textContent = '';

    let isValid = true;

    // Validate title: letters, numbers, spaces, hyphens, 5-150 chars
    const titleVal = form.title.value;
    const titleRegex = /^[a-zA-Z0-9\s\-]{5,150}$/;
    if (!titleRegex.test(titleVal)) {
        if(titleError) titleError.textContent = 'Title must be 5-150 chars and contain only letters, numbers, spaces, and hyphens.';
        isValid = false;
    }

    // Validate date: future date
    const dateVal = form.event_date.value;
    if (new Date(dateVal) <= new Date()) {
        if(dateError) dateError.textContent = 'Event date must be in the future.';
        isValid = false;
    }

    // Validate image: max 2MB, jpg/png/gif
    if (form.image && form.image.files.length > 0) {
        const file = form.image.files[0];
        if (file.size > 2 * 1024 * 1024) {
            if(imageError) imageError.textContent = 'Image size must be less than 2MB.';
            isValid = false;
        }
        if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
            if(imageError) imageError.textContent = 'Only JPG, PNG or GIF allowed.';
            isValid = false;
        }
    }

    return isValid;
}

function validateRegisterForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const emailVal = form.email.value;
    const pw = form.password.value;
    const pwc = form.confirm_password.value;
    
    const emailError = document.getElementById('email-error');
    const pwError = document.getElementById('pw-error');

    if(emailError) emailError.textContent = '';
    if(pwError) pwError.textContent = '';

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailVal)) {
        if(emailError) emailError.textContent = 'Invalid email format.';
        isValid = false;
    }

    if (pw !== pwc) {
        if(pwError) pwError.textContent = 'Passwords do not match.';
        isValid = false;
    }

    return isValid;
}

// Add real-time pw check
document.addEventListener('DOMContentLoaded', () => {
    const pwcField = document.getElementById('reg_confirm_password');
    if(pwcField) {
        pwcField.addEventListener('input', function() {
            const pwField = document.getElementById('reg_password');
            const pwError = document.getElementById('pw-match-msg');
            if (this.value !== pwField.value) {
                pwError.textContent = 'Passwords do not match.';
                pwError.className = 'text-danger small';
            } else {
                pwError.textContent = 'Passwords match.';
                pwError.className = 'text-success small';
            }
        });
    }
});

// Feature 7 & 8: dynamic rendering with callback pattern
function fetchEvents(callback) {
    fetch(BASE_URL + 'api/events.php?action=list')
        .then(response => response.json())
        .then(data => callback(data))
        .catch(err => console.error(err));
}

function renderEventCards(jsonData) {
    const container = document.getElementById('dynamic-events-container');
    if (!container) return;
    container.innerHTML = '';
    
    jsonData.forEach(event => {
        const cardHtml = `
            <div class="col-md-4 mb-4 event-card-item" data-type="${event.event_type}">
                <div class="card h-100">
                    <img src="${BASE_URL}${event.image_path}" class="card-img-top" alt="${event.title}" style="height:200px; object-fit:cover;">
                    <div class="card-body">
                        <span class="badge badge-${event.event_type} mb-2" data-tooltip="${event.event_type}">${event.event_type}</span>
                        <h5 class="card-title">${event.title}</h5>
                        <p class="card-text text-muted small"><i class="bi bi-calendar"></i> ${event.event_date}</p>
                        <p class="card-text text-muted small"><i class="bi bi-geo-alt"></i> ${event.location}</p>
                        <a href="${BASE_URL}event-detail.php?id=${event.id}" class="btn btn-outline-info btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += cardHtml;
    });
}
