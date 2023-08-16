var lastScrollTop = 0;

function openModal(modalTarget) {
  $('.jsModal').removeClass('is-open');
  var modalElement = $('#' + modalTarget);
  modalElement.addClass('is-open');
  modalElement.find('.jsModalDialog').addClass('is-animate');
  $('body').addClass('ov-hidden');
}

function closeModal() {
  $('.jsModal').removeClass('is-open is-animate');
  $('body').removeClass('ov-hidden');
}

$(document).ready(function () {
  var productSwiper = new Swiper(".jsProductSwiper", {
    slidesPerView: 2,
    loop: true,
    navigation: {
      nextEl: ".productSwiper .swiper-button-next",
      prevEl: ".productSwiper .swiper-button-prev"
    },
    pagination: {
      el: '.productSwiper .swiper-pagination',
      type: 'fraction',
      clickable: true
    },
    breakpoints: {
      480: {
        slidesPerView: 3
      },
      768: {
        slidesPerView: 4
      }
    }
  });

  var keylooksSwiper = new Swiper(".jsKeylooksSwiper", {
    loop: true,
    navigation: {
      nextEl: ".keylooks-swiper .swiper-button-next",
      prevEl: ".keylooks-swiper .swiper-button-prev"
    }
  });

  var datePicker = flatpickr(".jsReservationDatepicker",{
    
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    altInput: true,
    altFormat: "l, F j Y h:i K",
    onChange: function (selectedDates, dateStr, instance) {
      $(instance.input).closest('.jsReservationDatepickerContainer').next().show()
    }
    
  });

  var productDetailImageSwiper = new Swiper(".jsProductDetailImageSwiper", {
    loop: true,
    navigation: {
      nextEl: ".jsProductDetailImageSwiper .swiper-button-next",
      prevEl: ".jsProductDetailImageSwiper .swiper-button-prev"
    },
    on: {
      slideChange: function (index) {
        console.log(productDetailImageSwiper.realIndex);
        $('.jsProductDetailImageThumbItem').removeClass('is-active');
        $(".jsProductDetailImageThumbItem:eq(" + (productDetailImageSwiper.realIndex) + ")").addClass('is-active');;
      }
    }
  });

  $(".jsProductDetailImageThumbItem").mouseenter(function () {
    var index = $(".jsProductDetailImageThumbItem").index($(this))+1;
    $('.jsProductDetailImageThumbItem').removeClass('is-active');
    $(this).addClass('is-active');
    productDetailImageSwiper.slideTo(index, 500, false);
  });

  Number.prototype.format = function (n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
      num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
  };

  
  if ($("#slider-range").length > 0 ){
    $("#slider-range").slider({
      range: true,
      min: 199000,
      max: 2990000,
      values: [199000, 2990000],
      slide: function (event, ui) {
        $(".jsRangePriceFloor").val(ui.values[0].format(0, 3, '.', ','));
        $(".jsRangePriceCeiling").val(ui.values[1].format(0, 3, '.', ','));
      }
    });

    $(".jsRangePriceFloor").val($("#slider-range").slider("values", 0).format(0, 3, '.', ','));
    $(".jsRangePriceCeiling").val($("#slider-range").slider("values", 1).format(0, 3, '.', ','));
  }
  


  $('body').on('click', '.jsFilterBoxTrigger', function () {
    var others = $('.jsFilterBoxTrigger').not(this);
    others.closest('.jsFilterBox').removeClass('is-active');
    if ($(this).closest('.jsFilterBox').hasClass("is-active")) {
      $(this).closest('.jsFilterBox').removeClass("is-active");
    } else {
      $(this).closest('.jsFilterBox').addClass("is-active");
    }
  });

  $('body').on('click', '.jsFilterSortDropdownTrigger', function () {
    if ($(this).closest('.jsFilterSortDropdown').hasClass("is-active")) {
      $(this).closest('.jsFilterSortDropdown').removeClass("is-active");
    } else {
      $(this).closest('.jsFilterSortDropdown').addClass("is-active");
    }
  });

  $('body').on('click', '.jsSidebarCategoryDropdownTrigger', function () {
    if ($(this).closest('.jsSidebarCategoryDropdown').hasClass("is-open")) {
      $(this).closest('.jsSidebarCategoryDropdown').removeClass("is-open");
    } else {
      $(this).closest('.jsSidebarCategoryDropdown').addClass("is-open");
    }
  });

  $('body').on('click', '.jsStoreBoxTrigger', function () {
    if ($(this).closest('.jsStoreBox').hasClass("is-open")) {
      $(this).closest('.jsStoreBox').removeClass("is-open");
    } else {
      $(this).closest('.jsStoreBox').addClass("is-open");
    }
  });

  $(".jsFilterSwitch").change(function (e) {
    if ($(this).is(':checked')) {
      $('.jsFilterContainer').show();
    }
    else {
      $('.jsFilterContainer').hide();
    }
  });

  $(".jsReservationEmailInput").blur(function () {
    if($(this).val() !== ''){
      $(this).closest('.jsReservationEmail').addClass('is-valid');
      $('.jsReservationFormStepDefault').hide();
      $('.jsReservationFormStepRegister').show();
      if($(this).val() == 'babono@transdigital.co.id'){
        $('.jsReservationFormStepRegister').hide();
        $('.jsReservationFormStepLogin').show();
      }
    }
    else{
      $(this).closest('.jsReservationEmail').removeClass('is-valid');
      $('.jsReservationForm').hide();
      $('.jsReservationFormStepDefault').show();
    }
  });

  $(".jsReservationRegisterCheckbox").change(function (e) {
    if ($(this).is(':checked')) {
      $('.jsFormStepRegisterPassword').show();
    }
    else {
      $('.jsFormStepRegisterPassword').hide();
    }
  });

  $(".jsChangePasswordCheckbox").change(function (e) {
    if ($(this).is(':checked')) {
      $('.jsChangePasswordForm').show();
    }
    else {
      $('.jsChangePasswordForm').hide();
    }
  });
  

  $(".jsHeaderMenuMobile").on("click", function () {  
    $(this).toggleClass('is-close');
    $('.jsMobileNav').toggleClass('is-open');
    $('body').toggleClass('is-freeze');;
  });

  $(".jsHeaderSeachIcon").on("click", function () {
    $('.jsHeaderSeachInput').toggleClass('is-active');
  });


  $(".jsMobileNavItem").on("click", function () {
    if($(this).hasClass('is-active')){
      $(this).removeClass('is-active');
    }
    else{
      $(".jsMobileNavItem").removeClass('is-active');
      $(this).addClass('is-active');
    }
  });


  $(".jsHeaderNavItem").mouseenter(function () {
    $('.jsHeaderNavItem').removeClass('is-active');
    $(this).addClass('is-active');
  });

  $(".jsHeaderNavContainer").mouseleave(function () {
    $('.jsHeaderNavItem').removeClass('is-active');
  });

  // Hide Header on on scroll down
  var didScroll;
  var lastScrollTop = 0;
  var delta = 11;
  var navbarHeight = $(".jsHeader").outerHeight();



  $(window).scroll(function (event) {
    didScroll = true;
  });

  setInterval(function () {
    if (didScroll) {
      hasScrolled();
      if ($('.jsStickyNav').length > 0){
        stickyNav();
      }
      didScroll = false;
    }
  }, 250);

  function hasScrolled() {
    var st = $(this).scrollTop();

    // Make sure they scroll more than delta
    if (Math.abs(lastScrollTop - st) <= delta) return;

    // If they scrolled down and are past the navbar, add class .nav-up.
    // This is necessary so you never see what is "behind" the navbar.
    if (st > lastScrollTop && st > navbarHeight) {
      // Scroll Down
      $(".jsHeader").addClass("is-sticky");
      $(".jsSidebar").removeClass("is-down");
      
    } else {
      // Scroll Up
      if (st + $(window).height() < $(document).height()) {
        $(".jsHeader").removeClass("is-sticky")
        $(".jsSidebar").addClass("is-down");
      }
    }

    lastScrollTop = st;
  }

  function stickyNav() {
    var st = $(this).scrollTop();
    var batasButtonScroll = $('.jsProductDetailButton').offset().top + $('.jsProductDetailButton').outerHeight();
    console.log(batasButtonScroll);
    if (st > batasButtonScroll) {
      // Scroll Down
      $(".jsStickyNav").addClass("is-sticky");
    } else {
      // Scroll Up
      $(".jsStickyNav").removeClass("is-sticky")
    }
  }
});

