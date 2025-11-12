/**
 * CNP News Theme JavaScript
 * 
 * Main JavaScript file for CNP News theme functionality
 * Optimized for performance and Core Web Vitals
 * 
 * @package CNP_News
 * @version 1.0.0
 */

(function() {
    'use strict';
    
    // Theme namespace
    const CNPNews = {
        
        // Initialize theme
        init: function() {
            this.darkModeToggle();
            this.mobileMenu();
            this.searchToggle();
            this.readingProgress();
            this.calculateReadingTime();
            this.lazyLoading();
            this.performanceOptimizations();
            this.newsletterForms();
            this.scrollToTop();
            this.analyticsTracking();
        },
        
        // Dark mode toggle functionality
        darkModeToggle: function() {
            const toggle = document.querySelector('.cnp-theme-toggle');
            if (!toggle) return;
            
            // Check for saved theme preference or default to system preference
            const savedTheme = localStorage.getItem('cnp-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (systemPrefersDark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
            
            toggle.addEventListener('click', function() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('cnp-theme', newTheme);
                
                // Update toggle icon
                this.setAttribute('aria-pressed', newTheme === 'dark');
            });
        },
        
        // Mobile menu functionality
        mobileMenu: function() {
            const menuToggle = document.querySelector('.cnp-mobile-menu-toggle');
            const navigation = document.querySelector('.cnp-primary-nav');
            
            if (!menuToggle || !navigation) return;
            
            menuToggle.addEventListener('click', function() {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                this.setAttribute('aria-expanded', !isExpanded);
                navigation.classList.toggle('is-open');
                document.body.classList.toggle('menu-open');
                
                // Focus management
                if (!isExpanded) {
                    const firstLink = navigation.querySelector('a');
                    if (firstLink) firstLink.focus();
                }
            });
            
            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navigation.classList.contains('is-open')) {
                    navigation.classList.remove('is-open');
                    document.body.classList.remove('menu-open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    menuToggle.focus();
                }
            });
        },
        
        // Search toggle functionality
        searchToggle: function() {
            const searchToggle = document.querySelector('.cnp-search-toggle');
            const searchForm = document.querySelector('.cnp-search');
            
            if (!searchToggle || !searchForm) return;
            
            searchToggle.addEventListener('click', function() {
                searchForm.classList.toggle('is-active');
                const searchInput = searchForm.querySelector('input[type="search"]');
                if (searchInput && searchForm.classList.contains('is-active')) {
                    searchInput.focus();
                }
            });
        },
        
        // Reading progress indicator
        readingProgress: function() {
            if (!document.body.classList.contains('single-post')) return;
            
            const article = document.querySelector('.cnp-article-content');
            const progressBar = document.createElement('div');
            
            progressBar.className = 'cnp-reading-progress';
            progressBar.setAttribute('aria-hidden', 'true');
            document.body.appendChild(progressBar);
            
            function updateProgress() {
                if (!article) return;
                
                const articleTop = article.offsetTop;
                const articleHeight = article.offsetHeight;
                const windowHeight = window.innerHeight;
                const scrollTop = window.pageYOffset;
                
                const progress = Math.min(
                    Math.max((scrollTop - articleTop + windowHeight) / articleHeight, 0),
                    1
                );
                
                progressBar.style.transform = `scaleX(${progress})`;
            }
            
            window.addEventListener('scroll', this.throttle(updateProgress, 16));
        },

        // Calculate and display reading time
        calculateReadingTime: function() {
            if (!document.body.classList.contains('single-post')) return;

            const readingTimeElement = document.querySelector('.cnp-reading-time .reading-time-value');
            if (!readingTimeElement) return;

            // Try to get reading time from data attribute first (set by PHP)
            const postId = document.body.getAttribute('data-post-id');
            if (postId && window.cnp_ajax) {
                // Fetch reading time via AJAX if not already set
                fetch(window.cnp_ajax.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'get_reading_time',
                        post_id: postId,
                        nonce: window.cnp_ajax.nonce
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.reading_time) {
                        readingTimeElement.textContent = data.reading_time;
                    }
                })
                .catch(error => {
                    console.warn('Failed to fetch reading time:', error);
                    // Fallback to basic calculation
                    this.calculateReadingTimeFallback();
                });
            } else {
                // Fallback to basic calculation
                this.calculateReadingTimeFallback();
            }

            // Load dynamic content
            this.loadDynamicContent();
        },

        // Load dynamic content for tags, sources, and author bio
        loadDynamicContent: function() {
            const postId = document.body.getAttribute('data-post-id');
            if (!postId || !window.cnp_ajax) return;

            // Fetch all dynamic content at once
            fetch(window.cnp_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'get_dynamic_content',
                    post_id: postId,
                    nonce: window.cnp_ajax.nonce
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate tags
                    if (data.tags_html) {
                        const tagsContainer = document.getElementById('cnp-tags-container');
                        if (tagsContainer) {
                            tagsContainer.innerHTML = data.tags_html;
                        }
                    }

                    // Populate sources
                    if (data.sources_html) {
                        const sourcesContainer = document.getElementById('cnp-sources-container');
                        if (sourcesContainer) {
                            sourcesContainer.innerHTML = data.sources_html;
                        }
                    }

                    // Populate author bio
                    if (data.author_bio) {
                        const bioElement = document.getElementById('cnp-author-bio-content');
                        if (bioElement) {
                            bioElement.innerHTML = data.author_bio;
                        }
                    }

                    // Populate LinkedIn link
                    if (data.linkedin_html) {
                        const linkedinElement = document.getElementById('cnp-linkedin-link');
                        if (linkedinElement) {
                            linkedinElement.innerHTML = data.linkedin_html;
                        }
                    }
                }
            })
            .catch(error => {
                console.warn('Failed to load dynamic content:', error);
            });
        },

        // Fallback reading time calculation
        calculateReadingTimeFallback: function() {
            const readingTimeElement = document.querySelector('.cnp-reading-time .reading-time-value');
            if (!readingTimeElement || readingTimeElement.textContent.trim()) return;

            const articleContent = document.querySelector('.cnp-article-content');
            if (!articleContent) return;

            // Get text content and count words
            const text = articleContent.textContent || articleContent.innerText;
            const wordCount = text.trim().split(/\s+/).length;

            // Average reading speed: 225 words per minute (updated)
            const wordsPerMinute = 225;
            const readingTimeMinutes = Math.ceil(wordCount / wordsPerMinute);

            // Update the display
            readingTimeElement.textContent = readingTimeMinutes;
        },

        // Enhanced lazy loading for images
        lazyLoading: function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            
                            // Load image
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                            
                            // Load srcset
                            if (img.dataset.srcset) {
                                img.srcset = img.dataset.srcset;
                                img.removeAttribute('data-srcset');
                            }
                            
                            img.classList.remove('lazy');
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                }, {
                    rootMargin: '50px 0px'
                });
                
                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        },
        
        // Performance optimizations
        performanceOptimizations: function() {
            // Preload critical resources
            this.preloadCriticalResources();
            
            // Optimize third-party scripts
            this.optimizeThirdPartyScripts();
            
            // Monitor Core Web Vitals
            this.monitorWebVitals();
        },
        
        // Preload critical resources
        preloadCriticalResources: function() {
            // Preload hero image if present
            const heroImage = document.querySelector('.cnp-featured-image img');
            if (heroImage && heroImage.src) {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.as = 'image';
                link.href = heroImage.src;
                link.setAttribute('fetchpriority', 'high');
                document.head.appendChild(link);
            }
        },
        
        // Optimize third-party scripts
        optimizeThirdPartyScripts: function() {
            // Load non-critical scripts after user interaction
            const loadScriptsOnInteraction = () => {
                // Load analytics
                this.loadAnalytics();
                
                // Load social sharing widgets
                this.loadSocialWidgets();
                
                // Remove event listeners after first interaction
                ['mousedown', 'touchstart', 'keydown'].forEach(event => {
                    document.removeEventListener(event, loadScriptsOnInteraction, true);
                });
            };
            
            ['mousedown', 'touchstart', 'keydown'].forEach(event => {
                document.addEventListener(event, loadScriptsOnInteraction, true);
            });
        },
        
        // Load analytics
        loadAnalytics: function() {
            // This would be handled by CAOS plugin
            // Custom events can be tracked here
            if (typeof gtag !== 'undefined') {
                // Track newsletter signups
                document.querySelectorAll('.cnp-newsletter-form').forEach(form => {
                    form.addEventListener('submit', function() {
                        gtag('event', 'newsletter_signup', {
                            event_category: 'engagement',
                            event_label: 'newsletter'
                        });
                    });
                });
                
                // Track scroll depth
                this.trackScrollDepth();
            }
        },
        
        // Track scroll depth
        trackScrollDepth: function() {
            const milestones = [25, 50, 75, 100];
            const reached = new Set();
            
            const trackScrollMilestone = () => {
                const scrollPercent = Math.round(
                    (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100
                );
                
                milestones.forEach(milestone => {
                    if (scrollPercent >= milestone && !reached.has(milestone)) {
                        reached.add(milestone);
                        
                        if (typeof gtag !== 'undefined') {
                            gtag('event', `scroll_${milestone}`, {
                                event_category: 'engagement',
                                event_label: 'scroll_depth'
                            });
                        }
                    }
                });
            };
            
            window.addEventListener('scroll', this.throttle(trackScrollMilestone, 250));
        },
        
        // Monitor Core Web Vitals
        monitorWebVitals: function() {
            if ('PerformanceObserver' in window) {
                // Monitor LCP
                new PerformanceObserver((entryList) => {
                    for (const entry of entryList.getEntries()) {
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'web_vitals', {
                                event_category: 'Web Vitals',
                                event_label: 'LCP',
                                value: Math.round(entry.startTime)
                            });
                        }
                    }
                }).observe({ entryTypes: ['largest-contentful-paint'] });
                
                // Monitor FID/INP
                new PerformanceObserver((entryList) => {
                    for (const entry of entryList.getEntries()) {
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'web_vitals', {
                                event_category: 'Web Vitals',
                                event_label: entry.name === 'first-input' ? 'FID' : 'INP',
                                value: Math.round(entry.duration)
                            });
                        }
                    }
                }).observe({ entryTypes: ['first-input', 'event'] });
            }
        },
        
        // Newsletter form handling
        newsletterForms: function() {
            document.querySelectorAll('.cnp-newsletter-signup form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const email = this.querySelector('input[type="email"]').value;
                    const button = this.querySelector('button[type="submit"]');
                    const originalText = button.textContent;
                    
                    // Basic email validation
                    if (!CNPNews.isValidEmail(email)) {
                        CNPNews.showMessage('Please enter a valid email address.', 'error');
                        return;
                    }
                    
                    // Update button state
                    button.textContent = 'Subscribing...';
                    button.disabled = true;
                    
                    // Simulate API call (replace with actual endpoint)
                    setTimeout(() => {
                        button.textContent = 'Subscribed!';
                        CNPNews.showMessage('Thank you for subscribing!', 'success');
                        
                        setTimeout(() => {
                            button.textContent = originalText;
                            button.disabled = false;
                            form.reset();
                        }, 2000);
                    }, 1000);
                });
            });
        },
        
        // Scroll to top functionality
        scrollToTop: function() {
            const scrollButton = document.createElement('button');
            scrollButton.className = 'cnp-scroll-to-top';
            scrollButton.innerHTML = '↑';
            scrollButton.setAttribute('aria-label', 'Scroll to top');
            scrollButton.style.cssText = `
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background: var(--wp--preset--color--primary);
                color: white;
                border: none;
                cursor: pointer;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                z-index: 40;
            `;
            
            document.body.appendChild(scrollButton);
            
            const toggleScrollButton = () => {
                if (window.scrollY > 300) {
                    scrollButton.style.opacity = '1';
                    scrollButton.style.visibility = 'visible';
                } else {
                    scrollButton.style.opacity = '0';
                    scrollButton.style.visibility = 'hidden';
                }
            };
            
            window.addEventListener('scroll', this.throttle(toggleScrollButton, 100));
            
            scrollButton.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        },
        
        // Analytics tracking for engagement
        analyticsTracking: function() {
            // Track external link clicks
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.hostname !== window.location.hostname) {
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'click', {
                            event_category: 'External Link',
                            event_label: link.href
                        });
                    }
                }
            });
            
            // Track reading time
            if (document.body.classList.contains('single-post')) {
                let startTime = Date.now();
                let timeOnPage = 0;
                let milestones = [30, 60, 120, 300]; // seconds
                let reached = new Set();
                
                setInterval(() => {
                    timeOnPage = (Date.now() - startTime) / 1000;
                    
                    milestones.forEach(milestone => {
                        if (timeOnPage >= milestone && !reached.has(milestone)) {
                            reached.add(milestone);
                            
                            if (typeof gtag !== 'undefined') {
                                gtag('event', `engaged_time_${milestone}s`, {
                                    event_category: 'engagement',
                                    event_label: 'reading_time'
                                });
                            }
                        }
                    });
                }, 5000);
            }
        },
        
        // Utility functions
        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },
        
        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        
        showMessage: function(message, type = 'info') {
            const messageDiv = document.createElement('div');
            messageDiv.className = `cnp-message cnp-message--${type}`;
            messageDiv.textContent = message;
            messageDiv.style.cssText = `
                position: fixed;
                top: 2rem;
                right: 2rem;
                padding: 1rem 1.5rem;
                background: var(--wp--preset--color--${type === 'error' ? 'danger' : 'accent'});
                color: white;
                border-radius: 0.5rem;
                z-index: 1000;
                animation: slideInRight 0.3s ease;
            `;
            
            document.body.appendChild(messageDiv);
            
            setTimeout(() => {
                messageDiv.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    document.body.removeChild(messageDiv);
                }, 300);
            }, 3000);
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', CNPNews.init.bind(CNPNews));
    } else {
        CNPNews.init();
    }
    
    // Make CNPNews available globally for debugging
    window.CNPNews = CNPNews;
    
})();
