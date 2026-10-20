<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }} - {{ \App\Models\SystemSetting::get('instansi_name', 'Dinas Sosial PPPA') }}</title>
    <meta name="description"
        content="{{ \App\Models\SystemSetting::get('app_description', 'Website resmi Dinas Sosial PPPA Kabupaten Nganjuk. Informasi layanan, pengaduan, dan edukasi sosial.') }}">
    <meta name="keywords" content="Dinas Sosial, PPPA, Kabupaten Nganjuk, perlindungan anak, pemberdayaan perempuan">

    <!-- Favicons -->
    @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
        <link href="{{ asset($appLogo) }}" rel="icon">
        <link href="{{ asset($appLogo) }}" rel="apple-touch-icon">
    @else
        <link href="{{ asset('landingpage/assets/img/logo-sentra.png') }}" rel="icon">
        <link href="{{ asset('landingpage/assets/img/logo-sentr.png') }}" rel="apple-touch-icon">
    @endif

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('landingpage/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landingpage/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('landingpage/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('landingpage/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landingpage/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

    <!-- Main CSS File -->
    <link href="{{ asset('landingpage/assets/css/main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

</head>

<body>

    <main class="main">
        @yield ('content')
    </main>

    <a href="#header" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('landingpage/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ asset('landingpage/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Main JS File -->
    <script src="{{ asset('landingpage/assets/js/main.js') }}"></script>

</body>

<!-- SwiperJS CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>

document.addEventListener("DOMContentLoaded", function() {
  // Inisialisasi Swiper 3D untuk fitur aplikasi
  const fiturSwiper = new Swiper(".my-swiper-3d", {
    // Efek 3D coverflow
    effect: "coverflow",
    
    // Pengaturan dasar
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "auto",
    loop: true,
    
    // Pengaturan efek coverflow
    coverflowEffect: {
      rotate: 25,
      stretch: 90,
      depth: 300,
      modifier: 1.5,
      slideShadows: false,
      scale: 1
    },
    
    // Pengaturan autoplay
    autoplay: {
      delay: 4000,
      disableOnInteraction: false,
      pauseOnMouseEnter: true
    },
    
    // Pengaturan kecepatan transisi
    speed: 800,
    
    // Pagination
    pagination: {
      el: ".my-swiper-3d .swiper-pagination",
      clickable: true,
      dynamicBullets: true,
      renderBullet: function (index, className) {
        return '<span class="' + className + '"><span class="bullet-inner"></span></span>';
      }
    },
    
    // Navigation
    navigation: {
      nextEl: ".my-swiper-3d .swiper-button-next",
      prevEl: ".my-swiper-3d .swiper-button-prev"
    },
    
    // Pengaturan breakpoints untuk responsivitas
    breakpoints: {
      320: {
        slidesPerView: 1,
        coverflowEffect: {
          rotate: 15,
          depth: 200,
          modifier: 1
        }
      },
      640: {
        slidesPerView: 1,
        coverflowEffect: {
          rotate: 20,
          depth: 250,
          modifier: 1.2
        }
      },
      768: {
        slidesPerView: "auto",
        coverflowEffect: {
          rotate: 25,
          depth: 300,
          modifier: 1.5
        }
      }
    },
    
    // Event listeners
    on: {
      // Saat slide berubah
      slideChange: function() {
        // Saat slide berubah
        this.slides.forEach((slide, index) => {
          const overlay = slide.querySelector('.feature-overlay');
          if (overlay) {
            if (index === this.activeIndex) {
              overlay.style.transform = 'translateY(0)';
            } else {
              overlay.style.transform = 'translateY(100%)';
            }
          }
        });
      },
      
      // Saat swiper terinisialisasi
      init: function() {
        console.log('Swiper 3D berhasil diinisialisasi');
        
        // Tambahkan animasi entrance untuk slides
        this.slides.forEach((slide, index) => {
          slide.style.opacity = '0';
          slide.style.transform = 'translateY(50px)';
          
          setTimeout(() => {
            slide.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            slide.style.opacity = '1';
            slide.style.transform = 'translateY(0)';
          }, index * 100);
        });
      },
      
      // Saat swiper resize
      resize: function() {
        this.update();
      }
    }
  });
  
  // Pause autoplay saat mouse hover pada swiper
  const swiperContainer = document.querySelector('.my-swiper-3d');
  if (swiperContainer) {
    swiperContainer.addEventListener('mouseenter', () => {
      fiturSwiper.autoplay.stop();
    });
    
    swiperContainer.addEventListener('mouseleave', () => {
      fiturSwiper.autoplay.start();
    });
  }
  
  // Animasi counter untuk stats
  const animateCounter = (element, target, duration = 2000) => {
    let start = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
      start += increment;
      if (start >= target) {
        element.textContent = target;
        clearInterval(timer);
      } else {
        if (target >= 1000) {
          element.textContent = Math.floor(start / 1000) + 'K+';
        } else if (target.toString().includes('.')) {
          element.textContent = start.toFixed(1) + '★';
        } else {
          element.textContent = Math.floor(start) + '+';
        }
      }
    }, 16);
  };
  
  // Intersection Observer untuk animasi stats
  const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const statsItems = entry.target.querySelectorAll('.stat-item h3');
        const values = [50000, 1000, 4.8]; // Sesuaikan dengan nilai yang diinginkan
        
        statsItems.forEach((item, index) => {
          setTimeout(() => {
            animateCounter(item, values[index], 1500);
          }, index * 200);
        });
        
        statsObserver.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  // Observe stats section
  const statsSection = document.querySelector('.stats-section');
  if (statsSection) {
    statsObserver.observe(statsSection);
  }
  
  // Smooth scroll untuk link internal (jika ada)
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
  
  // Parallax effect untuk background elements
  window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.floating-element');
    
    parallaxElements.forEach((element, index) => {
      const speed = 0.1 + (index * 0.1);
      element.style.transform = `translateY(${scrolled * speed}px) rotate(${scrolled * 0.05}deg)`;
    });
  });
  
  // Lazy loading untuk gambar (optional enhancement)
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src || img.src;
          img.classList.remove('lazy');
          imageObserver.unobserve(img);
        }
      });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img);
    });
  }
  
  // Error handling untuk Swiper
  if (typeof Swiper === 'undefined') {
    console.error('Swiper library tidak ditemukan. Pastikan CDN Swiper sudah dimuat.');
  }
});

/* =================================
   UTILITY FUNCTIONS
================================= */

// Fungsi untuk mendeteksi device mobile
function isMobile() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Fungsi untuk throttle scroll event
function throttle(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Optimasi scroll event dengan throttling
const optimizedScroll = throttle(() => {
  const scrolled = window.pageYOffset;
  const parallaxElements = document.querySelectorAll('.floating-element');
  
  parallaxElements.forEach((element, index) => {
    const speed = 0.1 + (index * 0.1);
    const yPos = scrolled * speed;
    const rotation = scrolled * 0.05;
    element.style.transform = `translate3d(0, ${yPos}px, 0) rotate(${rotation}deg)`;
  });
}, 16);

// Ganti scroll listener dengan yang sudah dioptimasi
window.addEventListener('scroll', optimizedScroll, { passive: true });

// Fungsi untuk mengatur volume autoplay berdasarkan visibility
function handleVisibilityChange() {
  const swiperInstance = document.querySelector('.my-swiper-3d').swiper;
  
  if (document.hidden) {
    // Halaman tidak terlihat, pause autoplay
    if (swiperInstance && swiperInstance.autoplay) {
      swiperInstance.autoplay.stop();
    }
  } else {
    // Halaman terlihat, resume autoplay
    if (swiperInstance && swiperInstance.autoplay) {
      swiperInstance.autoplay.start();
    }
  }
}

// Listen untuk perubahan visibility halaman
document.addEventListener('visibilitychange', handleVisibilityChange);

// Fungsi untuk menambahkan loading state
function addLoadingState() {
  const swiperContainer = document.querySelector('.my-swiper-3d');
  if (swiperContainer) {
    swiperContainer.classList.add('swiper-loading');
    
    // Hapus loading state setelah semua gambar dimuat
    const images = swiperContainer.querySelectorAll('img');
    let loadedImages = 0;
    
    const checkAllImagesLoaded = () => {
      loadedImages++;
      if (loadedImages === images.length) {
        setTimeout(() => {
          swiperContainer.classList.remove('swiper-loading');
          swiperContainer.classList.add('swiper-loaded');
        }, 500);
      }
    };
    
    images.forEach(img => {
      if (img.complete) {
        checkAllImagesLoaded();
      } else {
        img.addEventListener('load', checkAllImagesLoaded);
        img.addEventListener('error', checkAllImagesLoaded);
      }
    });
  }
}

// Panggil fungsi loading state
addLoadingState();

// Fungsi untuk keyboard navigation
function setupKeyboardNavigation() {
  document.addEventListener('keydown', (e) => {
    const swiperInstance = document.querySelector('.my-swiper-3d').swiper;
    
    if (swiperInstance) {
      switch(e.key) {
        case 'ArrowLeft':
          e.preventDefault();
          swiperInstance.slidePrev();
          break;
        case 'ArrowRight':
          e.preventDefault();
          swiperInstance.slideNext();
          break;
        case ' ': // Spacebar untuk pause/play
          e.preventDefault();
          if (swiperInstance.autoplay.running) {
            swiperInstance.autoplay.stop();
          } else {
            swiperInstance.autoplay.start();
          }
          break;
      }
    }
  });
}

// Setup keyboard navigation
setupKeyboardNavigation();

// Fungsi untuk mengatur focus accessibility
function setupAccessibility() {
  const swiperSlides = document.querySelectorAll('.swiper-slide');
  
  swiperSlides.forEach((slide, index) => {
    slide.setAttribute('role', 'tabpanel');
    slide.setAttribute('aria-label', `Fitur aplikasi ${index + 1}`);
    
    // Tambahkan tabindex untuk keyboard navigation
    const phoneElement = slide.querySelector('.phone-mockup');
    if (phoneElement) {
      phoneElement.setAttribute('tabindex', '0');
      
      // Tambahkan focus handler
      phoneElement.addEventListener('focus', () => {
        slide.scrollIntoView({ behavior: 'smooth', block: 'center' });
      });
    }
  });
  
  // Setup ARIA labels untuk navigation
  const nextBtn = document.querySelector('.swiper-button-next');
  const prevBtn = document.querySelector('.swiper-button-prev');
  
  if (nextBtn) nextBtn.setAttribute('aria-label', 'Slide berikutnya');
  if (prevBtn) prevBtn.setAttribute('aria-label', 'Slide sebelumnya');
  
  // Setup focus untuk image containers
  const imageContainers = document.querySelectorAll('.image-container');
  imageContainers.forEach((container, index) => {
    container.setAttribute('tabindex', '0');
    container.setAttribute('role', 'button');
    container.setAttribute('aria-label', `Fitur aplikasi ${index + 1}`);
  });
}

// Setup accessibility
setupAccessibility();

// Fungsi untuk performance monitoring
function monitorPerformance() {
  if ('performance' in window) {
    const observer = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        if (entry.entryType === 'measure' && entry.name.includes('swiper')) {
          console.log(`Swiper performance: ${entry.duration.toFixed(2)}ms`);
        }
      }
    });
    
    observer.observe({ entryTypes: ['measure'] });
  }
}

// Monitor performance (hanya di development)
if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
  monitorPerformance();
}

// Fungsi untuk resize handler
const handleResize = throttle(() => {
  const swiperInstance = document.querySelector('.my-swiper-3d').swiper;
  if (swiperInstance) {
    swiperInstance.update();
    swiperInstance.updateSize();
    swiperInstance.updateSlides();
  }
}, 250);

window.addEventListener('resize', handleResize);

// Cleanup functions untuk memory leaks prevention
window.addEventListener('beforeunload', () => {
  const swiperInstance = document.querySelector('.my-swiper-3d').swiper;
  if (swiperInstance) {
    swiperInstance.destroy(true, true);
  }
  
  // Hapus semua event listeners
  window.removeEventListener('scroll', optimizedScroll);
  window.removeEventListener('resize', handleResize);
  document.removeEventListener('visibilitychange', handleVisibilityChange);
});

// Fungsi untuk custom events
function dispatchCustomEvents() {
  // Event ketika swiper selesai loading
  const swiperLoadedEvent = new CustomEvent('swiperLoaded', {
    detail: { timestamp: Date.now() }
  });
  
  // Dispatch event setelah swiper terinisialisasi
  setTimeout(() => {
    document.dispatchEvent(swiperLoadedEvent);
  }, 1000);
}

dispatchCustomEvents();

// Error boundary untuk JavaScript
window.addEventListener('error', (e) => {
  console.error('Error dalam fitur section:', e.error);
  
  // Fallback jika swiper gagal
  const swiperContainer = document.querySelector('.my-swiper-3d');
  if (swiperContainer && !swiperContainer.swiper) {
    console.warn('Swiper gagal diinisialisasi, menggunakan fallback...');
    swiperContainer.classList.add('swiper-fallback');
  }
});

// Console log untuk debugging (akan dihapus di production)
if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
  console.log('%c🚀 Fitur Section Loaded Successfully! ', 'background: #059652; color: white; padding: 5px 10px; border-radius: 5px;');
  console.log('Available features:');
  console.log('- 3D Swiper dengan coverflow effect');
  console.log('- Autoplay dengan pause on hover');
  console.log('- Keyboard navigation (Arrow keys, Spacebar)');
  console.log('- Mobile responsive');
  console.log('- Accessibility support');
  console.log('- Performance optimized');
}
</script>