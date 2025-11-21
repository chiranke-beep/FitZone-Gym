// Initialize GSAP and ScrollTrigger
gsap.registerPlugin(ScrollTrigger);

// Utility: Check if mobile device
const isMobile = () => window.innerWidth <= 768;

// Header: Sticky effect, mobile menu, navigation
const header = document.querySelector('.header');
const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const navbar = document.querySelector('.navbar');
const navLinks = document.querySelectorAll('.navbar a[href^="#"]');

// Sticky header effect
gsap.to(header, {
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    boxShadow: '0 2px 10px rgba(0, 0, 0, 0.1)',
    duration: 0.3,
    ease: 'power2.out',
    scrollTrigger: {
        trigger: header,
        start: 'top top',
        end: '+=50',
        scrub: true,
    },
});

// Mobile menu toggle
if (mobileMenuBtn && navbar) {
    mobileMenuBtn.addEventListener('click', () => {
        const isOpen = navbar.classList.contains('active');
        navbar.classList.toggle('active');
        mobileMenuBtn.querySelector('i').classList.toggle('fa-bars');
        mobileMenuBtn.querySelector('i').classList.toggle('fa-times');
        gsap.to(navbar, {
            height: isOpen ? 0 : 'auto',
            opacity: isOpen ? 0 : 1,
            duration: 0.4,
            ease: 'power3.inOut',
        });
        if (!isOpen) {
            gsap.from('.navbar.active li', {
                y: -10,
                opacity: 0,
                duration: 0.3,
                stagger: 0.1,
                ease: 'power3.out',
                delay: 0.2,
            });
        }
    });

    // Handle window resize to ensure menu state is correct
    window.addEventListener('resize', () => {
        if (!isMobile() && navbar.classList.contains('active')) {
            navbar.classList.remove('active');
            mobileMenuBtn.querySelector('i').classList.add('fa-bars');
            mobileMenuBtn.querySelector('i').classList.remove('fa-times');
            gsap.to(navbar, { height: 0, opacity: 0, duration: 0.4, ease: 'power3.inOut' });
        }
    });

    // Ensure navbar links close mobile menu
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbar.classList.contains('active')) {
                navbar.classList.remove('active');
                mobileMenuBtn.querySelector('i').classList.toggle('fa-bars');
                mobileMenuBtn.querySelector('i').classList.toggle('fa-times');
                gsap.to(navbar, { height: 0, opacity: 0, duration: 0.4, ease: 'power3.inOut' });
            }
        });
    });
}

// Smooth scrolling for nav links
navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href').substring(1);
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
            const headerHeight = header ? header.offsetHeight : 70;
            window.scrollTo({
                top: targetSection.offsetTop - headerHeight,
                behavior: 'smooth'
            });
            // Update active class
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        } else {
            console.error(`Section #${targetId} not found`);
        }
    });
});

// Update active link on scroll
window.addEventListener('scroll', () => {
    let current = '';
    const sections = document.querySelectorAll('section[id]');
    const headerHeight = header ? header.offsetHeight : 70;
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        if (window.scrollY >= sectionTop - headerHeight - 50) {
            current = section.getAttribute('id');
        }
    });
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
});

// Hero: Entrance animations
gsap.from('.hero-content h1', { y: 50, opacity: 0, duration: 1, ease: 'power3.out', delay: 0.2 });
gsap.from('.hero-content p', { y: 30, opacity: 0, duration: 1, ease: 'power3.out', delay: 0.4 });
gsap.from('.hero-buttons', { y: 20, opacity: 0, duration: 1, ease: 'power3.out', delay: 0.6 });
gsap.from('.hero-image .main-image', { x: 100, opacity: 0, duration: 1.2, ease: 'power3.out', delay: 0.3 });
gsap.from('.achievement-badge', { scale: 0.8, opacity: 0, duration: 0.8, stagger: 0.2, ease: 'back.out(1.7)', delay: 0.5 });

// Stats: Staggered reveal
document.querySelectorAll('.stat-card').forEach((card, index) => {
    gsap.from(card, {
        y: 50,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out',
        delay: index * 0.2,
        scrollTrigger: { trigger: card, start: 'top 85%' },
    });
});

// About: Content animations
gsap.from('.about-images img', {
    y: 100,
    opacity: 0,
    duration: 1.2,
    ease: 'power3.out',
    stagger: 0.2,
    scrollTrigger: { trigger: '.about-images', start: 'top 80%' },
});
gsap.from('.about-content > *', {
    y: 30,
    opacity: 0,
    duration: 0.8,
    stagger: 0.2,
    ease: 'power3.out',
    scrollTrigger: { trigger: '.about-content', start: 'top 80%' },
});

// Services: Card animations
document.querySelectorAll('.service-card').forEach((card, index) => {
    gsap.from(card, {
        y: 50,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out',
        delay: index * 0.15,
        scrollTrigger: { trigger: card, start: 'top 85%' },
    });
});

// Classes: Tab filtering and card animations
const tabButtons = document.querySelectorAll('.class-tabs .tab-btn');
const classCards = document.querySelectorAll('.class-card');
tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const category = btn.dataset.category;
        gsap.to(classCards, {
            opacity: 0,
            y: 20,
            duration: 0.3,
            ease: 'power2.in',
            onComplete: () => {
                classCards.forEach(card => {
                    card.style.display = (category === 'all' || card.dataset.category === category) ? 'block' : 'none';
                    if (card.style.display === 'block') {
                        gsap.to(card, { opacity: 1, y: 0, duration: 0.5, ease: 'power3.out' });
                    }
                });
            },
        });
    });
});
document.querySelectorAll('.class-card').forEach((card, index) => {
    gsap.from(card, {
        y: 50,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out',
        delay: index * 0.1,
        scrollTrigger: { trigger: card, start: 'top 85%' },
    });
});

// Trainers: Card animations
document.querySelectorAll('.trainer-card').forEach((card, index) => {
    gsap.from(card, {
        y: 50,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out',
        delay: index * 0.15,
        scrollTrigger: { trigger: card, start: 'top 85%' },
    });
});

// Membership: Card animations
document.querySelectorAll('.plan-card').forEach((card, index) => {
    gsap.from(card, {
        y: 50,
        opacity: 0,
        scale: 0.95,
        duration: 0.8,
        ease: 'power3.out',
        delay: index * 0.3,
        scrollTrigger: { trigger: card, start: 'top 85%' },
    });
});

// Testimonials: Slider
const slides = document.querySelectorAll('.testimonial-slide');
const prevBtn = document.querySelector('.slider-prev');
const nextBtn = document.querySelector('.slider-next');
const dots = document.querySelectorAll('.slider-dots .dot');
let currentIndex = 0;
let autoSlideInterval;

function updateSlider(index) {
    gsap.to(slides[currentIndex], { xPercent: -100, opacity: 0, duration: 0.6, ease: 'power3.in' });
    gsap.fromTo(slides[index], 
        { xPercent: 100, opacity: 0 },
        { xPercent: 0, opacity: 1, duration: 0.6, ease: 'power3.out',
            onStart: () => {
                slides.forEach((s, i) => s.classList.toggle('active', i === index));
                dots.forEach((d, i) => d.classList.toggle('active', i === index));
            }
        }
    );
    gsap.from(slides[index].querySelector('.testimonial-author img'), {
        x: -30,
        opacity: 0,
        duration: 0.5,
        ease: 'power3.out',
        delay: 0.2,
    });
    gsap.from(slides[index].querySelector('.testimonial-text'), {
        y: 20,
        opacity: 0,
        duration: 0.5,
        ease: 'power3.out',
        delay: 0.3,
    });
    currentIndex = index;
}

function nextSlide() {
    let newIndex = currentIndex + 1;
    if (newIndex >= slides.length) newIndex = 0;
    updateSlider(newIndex);
}

function prevSlide() {
    let newIndex = currentIndex - 1;
    if (newIndex < 0) newIndex = slides.length - 1;
    updateSlider(newIndex);
}

function startAutoSlide() {
    clearInterval(autoSlideInterval);
    autoSlideInterval = setInterval(nextSlide, 5000);
}

function stopAutoSlide() {
    clearInterval(autoSlideInterval);
}

if (prevBtn && nextBtn && dots.length) {
    prevBtn.addEventListener('click', () => { stopAutoSlide(); prevSlide(); startAutoSlide(); });
    nextBtn.addEventListener('click', () => { stopAutoSlide(); nextSlide(); startAutoSlide(); });
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => { stopAutoSlide(); updateSlider(index); startAutoSlide(); });
    });
    document.querySelector('.testimonial-slider').addEventListener('mouseenter', stopAutoSlide);
    document.querySelector('.testimonial-slider').addEventListener('mouseleave', startAutoSlide);
    startAutoSlide();
}

// CTA: Entrance animations
gsap.from('.cta-content > *', {
    y: 30,
    opacity: 0,
    duration: 0.8,
    stagger: 0.2,
    ease: 'power3.out',
    scrollTrigger: { trigger: '.cta-content', start: 'top 80%' },
});
gsap.from('.cta-image img', {
    x: 50,
    opacity: 0,
    duration: 1,
    ease: 'power3.out',
    scrollTrigger: { trigger: '.cta-image', start: 'top 80%' },
});

// Blog: Card animations
document.querySelectorAll('.blog-card').forEach((card, index) => {
    gsap.from(card, {
        y: 50,
        opacity: 0,
        duration: 0.8,
        ease: 'power3.out',
        delay: index * 0.15,
        scrollTrigger: { trigger: card, start: 'top 85%' },
    });
});

// Footer: Fade-in
gsap.from('.footer-col', {
    y: 30,
    opacity: 0,
    duration: 0.8,
    stagger: 0.2,
    ease: 'power3.out',
    scrollTrigger: { trigger: '.footer-grid', start: 'top 85%' },
});

// Back to Top: Scale-in
const backToTop = document.querySelector('.back-to-top');
gsap.set(backToTop, { opacity: 0, scale: 0 });
gsap.to(backToTop, {
    opacity: 1,
    scale: 1,
    duration: 0.5,
    ease: 'power3.out',
    scrollTrigger: {
        trigger: document.body,
        start: '1000px top',
        end: '1000px top',
        toggleActions: 'play none none reverse',
    },
});
backToTop.addEventListener('click', (e) => {
    e.preventDefault();
    gsap.to(window, { scrollTo: 0, duration: 1, ease: 'power3.inOut' });
});