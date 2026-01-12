/**
 * Boutique Theme - Animaciones y Efectos Interactivos
 * =====================================================
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // 1. Animación de entrada - Fade In Up
    // ========================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Aplicar observador a cards
    document.querySelectorAll('.card, .kpi-card').forEach(card => {
        observer.observe(card);
    });

    // ========================================
    // 2. Efecto Ripple en botones
    // ========================================
    const buttons = document.querySelectorAll('button:not(.btn-close)');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            // Remover ripple anterior si existe
            const existingRipple = this.querySelector('.ripple');
            if (existingRipple) {
                existingRipple.remove();
            }

            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // ========================================
    // 3. Efecto Hover en Cards
    // ========================================
    const cards = document.querySelectorAll('.card, .kpi-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });

    // ========================================
    // 4. Animación de números contadores (KPIs)
    // ========================================
    function animateCounter(element, target, duration = 1500) {
        let start = 0;
        const increment = target / (duration / 16);
        
        const counter = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target.toLocaleString('es-PE', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
                clearInterval(counter);
            } else {
                element.textContent = Math.floor(start).toLocaleString('es-PE', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }
        }, 16);
    }

    // Observar elementos con clase kpi-value para animar
    const kpiValues = document.querySelectorAll('.kpi-value');
    const kpiObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const text = entry.target.textContent.trim();
                const numberMatch = text.match(/\d+(\.\d+)?/);
                
                if (numberMatch) {
                    const targetNumber = parseFloat(numberMatch[0]);
                    if (!isNaN(targetNumber)) {
                        entry.target.textContent = '0';
                        animateCounter(entry.target, targetNumber);
                    }
                }
                kpiObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    kpiValues.forEach(kpi => kpiObserver.observe(kpi));

    // ========================================
    // 5. Scroll suave para enlaces internos
    // ========================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // ========================================
    // 6. Agregar clase active a enlace del menú actual
    // ========================================
    const currentUrl = window.location.href;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        if (link.href === currentUrl) {
            link.classList.add('active');
        }
    });

    // ========================================
    // 7. Efecto de carga en tablas
    // ========================================
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            row.style.animation = `fadeInUp 0.5s ease ${index * 0.1}s forwards`;
            row.style.opacity = '0';
        });
    });

    // ========================================
    // 8. Validación en tiempo real de formularios
    // ========================================
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('change', function() {
            const inputs = this.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                if (input.value.trim()) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            });
        });
    });

    // ========================================
    // 9. Tooltip mejorado con Popper
    // ========================================
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });

    // ========================================
    // 10. Notificación de estado de conexión
    // ========================================
    window.addEventListener('offline', function() {
        const alert = document.createElement('div');
        alert.className = 'alert alert-warning alert-dismissible fade show position-fixed';
        alert.style.bottom = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '9999';
        alert.innerHTML = `
            <i class="fas fa-wifi-off me-2"></i>
            <strong>¡Conexión perdida!</strong> Parece que no hay conexión a internet.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alert);
    });

    window.addEventListener('online', function() {
        const alerts = document.querySelectorAll('.alert-warning');
        alerts.forEach(alert => {
            if (alert.textContent.includes('Conexión perdida')) {
                alert.remove();
            }
        });
    });

    // ========================================
    // 11. Smooth scroll al cargar página con hash
    // ========================================
    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 500);
        }
    }

    // ========================================
    // 12. Efecto de escritura en tipografías principales
    // ========================================
    function typewriterEffect(element, speed = 30) {
        const text = element.textContent;
        element.textContent = '';
        let i = 0;

        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }

        type();
    }

    // Aplicar a h1 si deseas
    // const mainTitle = document.querySelector('h1');
    // if (mainTitle) typewriterEffect(mainTitle);

    console.log('✨ Boutique Theme - Animaciones cargadas correctamente');
});

/**
 * Estilos CSS adicionales para animaciones
 * Se pueden agregar a boutique-theme.css si lo deseas
 */
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Animación de carga */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Animación de pulso */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    /* Animación de shake */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }

    /* Scroll suave global */
    html {
        scroll-behavior: smooth;
    }

    /* Transición de página */
    body {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Validación de formulario */
    .form-control.is-valid {
        border-color: #B29F8C;
        box-shadow: 0 0 0 0.2rem rgba(178, 159, 140, 0.25);
    }

    .form-control.is-valid:focus {
        border-color: #B29F8C;
        box-shadow: 0 0 0 0.2rem rgba(178, 159, 140, 0.25);
    }

    /* Selector activo */
    .nav-link.active {
        border-left: 3px solid #D4AF37 !important;
    }
`;

document.head.appendChild(styleSheet);
