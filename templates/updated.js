// assets/js/main.js

document.addEventListener('DOMContentLoaded', function() {
    // Load portfolio projects
    fetchPortfolio();
    
    // Load skills
    fetchSkills();
    
    // Load testimonials
    fetchTestimonials();
    
    // Update form submissions to use AJAX
    setupFormSubmissions();
});

// Fetch portfolio projects from backend
async function fetchPortfolio() {
    try {
        const response = await fetch('backend/api/portfolio.php');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            const container = document.getElementById('portfolio-container');
            if (container) {
                container.innerHTML = data.data.map(project => `
                    <div class="portfolio-item">
                        <img src="${project.image_url}" alt="${project.title}" class="portfolio-img">
                        <div class="portfolio-info">
                            <h3>${project.title}</h3>
                            <p>${project.short_description || project.description.substring(0, 150)}...</p>
                            <a href="${project.project_url}" target="_blank" class="btn">Visit Website</a>
                        </div>
                    </div>
                `).join('');
            }
        }
    } catch (error) {
        console.error('Error loading portfolio:', error);
    }
}

// Fetch skills from backend
async function fetchSkills() {
    try {
        const response = await fetch('backend/api/skills.php');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            const container = document.getElementById('skills-container');
            if (container) {
                container.innerHTML = `
                    <h3 style="color: var(--primary); margin-bottom: 20px;">My Skills</h3>
                    ${data.data.map(skill => `
                        <div class="skill-item">
                            <div class="skill-name">
                                <span>${skill.skill_name}</span>
                                <span>${skill.proficiency_level}%</span>
                            </div>
                            <div class="skill-bar">
                                <div class="skill-level" style="width: ${skill.proficiency_level}%;"></div>
                            </div>
                        </div>
                    `).join('')}
                `;
            }
        }
    } catch (error) {
        console.error('Error loading skills:', error);
    }
}

// Fetch testimonials from backend
async function fetchTestimonials() {
    try {
        const response = await fetch('backend/api/testimonials.php');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            const container = document.getElementById('testimonials-container');
            if (container) {
                container.innerHTML = data.data.map(testimonial => `
                    <div class="testimonial-item">
                        <div class="testimonial-content">
                            <p>"${testimonial.content}"</p>
                        </div>
                        <div class="testimonial-author">
                            <h4>${testimonial.person_name}</h4>
                            <p>${testimonial.person_title}${testimonial.person_company ? ` at ${testimonial.person_company}` : ''}</p>
                            <div class="rating">
                                ${'★'.repeat(testimonial.rating)}${'☆'.repeat(5 - testimonial.rating)}
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }
    } catch (error) {
        console.error('Error loading testimonials:', error);
    }
}

// Setup form submissions with AJAX
function setupFormSubmissions() {
    const mentoringForm = document.getElementById('mentoringForm');
    const contactForm = document.getElementById('contactForm');
    
    if (mentoringForm) {
        mentoringForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            // Convert checkbox values to booleans
            data.agreed_to_terms = formData.has('terms');
            data.subscribe_newsletter = formData.has('newsletter');
            
            try {
                const response = await fetch('backend/api/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    mentoringForm.reset();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Network error. Please try again.');
                console.error('Submission error:', error);
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('backend/api/contact.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    contactForm.reset();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Network error. Please try again.');
                console.error('Submission error:', error);
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }
}