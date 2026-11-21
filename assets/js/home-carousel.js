document.addEventListener('DOMContentLoaded', () => {
  const root = document.querySelector('[data-home-carousel]')
  if (!root || typeof Swiper === 'undefined') return

  const carousel = root.querySelector('.home-courses-swiper')
  const originalSlides = Array.from(carousel?.querySelectorAll('.swiper-slide') || [])
  const totalSlides = originalSlides.length
  if (!carousel || totalSlides === 0) return

  originalSlides.forEach((slide, index) => {
    slide.dataset.courseIndex = String(index)
  })

  const counter = root.querySelector('[data-carousel-counter]')
  const detail = (key) => root.querySelector(`[data-course-detail="${key}"]`)

  const updateDetail = (swiper) => {
    const slide = swiper.slides[swiper.activeIndex]
    if (!slide) return

    const data = slide.dataset
    const values = {
      title: data.courseTitle,
      description: data.courseDescription,
      level: data.courseLevel,
      instructor: data.courseInstructor,
      wellbeing: data.courseWellbeing,
      duration: data.courseDuration,
      rating: data.courseRating,
    }

    Object.entries(values).forEach(([key, value]) => {
      const node = detail(key)
      if (node) node.textContent = value || ''
    })

    const href = detail('href')
    if (href) href.setAttribute('href', data.courseHref || '#')

    if (counter) {
      const index = Number(data.courseIndex ?? swiper.realIndex)
      counter.textContent = `${String(index + 1).padStart(2, '0')} / ${String(totalSlides).padStart(2, '0')}`
    }
  }

  const swiper = new Swiper(carousel, {
    centeredSlides: true,
    loop: totalSlides > 1,
    loopAdditionalSlides: totalSlides,
    loopPreventsSliding: false,
    slideToClickedSlide: true,
    grabCursor: true,
    initialSlide: totalSlides > 1 ? 1 : 0,
    slidesPerView: 1.08,
    spaceBetween: 14,
    speed: 480,
    keyboard: { enabled: true },
    breakpoints: {
      640: { slidesPerView: 1.62, spaceBetween: 18 },
      900: { slidesPerView: 1.9, spaceBetween: 20 },
      1180: { slidesPerView: 2.18, spaceBetween: 22 },
    },
    on: {
      init(instance) { updateDetail(instance) },
      slideChange(instance) { updateDetail(instance) },
      loopFix(instance) { updateDetail(instance) },
    },
  })

  root.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
    if (totalSlides > 1) swiper.slidePrev()
  })

  root.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
    if (totalSlides > 1) swiper.slideNext()
  })
})
